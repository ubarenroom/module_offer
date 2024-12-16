<?php

namespace Dnd\OfferManager\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;

class ImageUploader
{

    public const BASE_TMP_PATH = "wysiwyg/tmp/offer/";
    public const BASE_PATH = "wysiwyg/offer/";
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * @var string
     */
    public $baseTmpPath;
    /**
     * @var string
     */
    public $basePath;
    /**
     * @var string[]
     */
    public $allowedExtensions;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @throws \Magento\Framework\Exception\FileSystemException
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private Database $coreFileStorageDatabase,
        private Filesystem $filesystem,
        private UploaderFactory $uploaderFactory,
        private StoreManagerInterface $storeManager,
        private LoggerInterface $logger
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->baseTmpPath = self::BASE_TMP_PATH;
        $this->basePath = self::BASE_PATH;
        $this->allowedExtensions = self::ALLOWED_EXTENSIONS;
    }

    /**
     * Move File From Tmp
     *
     * @param string $imageName
     * @return mixed
     * @throws LocalizedException
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);
        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }

    /**
     * Get Base Tmp Path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    /**
     * Set Base Tmp Path
     *
     * @param string $baseTmpPath
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * Get Base Path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Set Base Path
     *
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Get File Path
     *
     * @param string $path
     * @param string $imageName
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Save File to tmp dir
     *
     * @param string $fileId
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];
        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
        return $result;
    }

    /**
     * Get Allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Set Allowed extensions
     *
     * @param string $allowedExtensions
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Save Media Image
     *
     * @param string $imageName
     * @param string $imagePath
     * @return mixed
     * @throws LocalizedException
     */
    public function saveMediaImage($imageName, $imagePath)
    {
        $basePath = $this->getBasePath();
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $mediaPath = substr($imagePath, 0, strpos($imagePath, 'media'));
        $baseTmpImagePath = str_replace($mediaPath . 'media/', '', $imagePath);

        if ($baseImagePath == $baseTmpImagePath) {
            return $imageName;
        }
        try {
            $this->mediaDirectory->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $baseImagePath;
    }
}
