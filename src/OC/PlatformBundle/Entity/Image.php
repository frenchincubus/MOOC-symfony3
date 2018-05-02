<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    private $file;

    private $tempName;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function getFile()
    {
      return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
      $this->file = $file;

      if (null !== $this->url)
      {
        $this->tempName = $this->url;

        $this->url = null;
        $this->alt = null;
      }
    }

    /**
    * @ORM\PrePersist()
    *@ORM\PreUpdate()
    */
    public function preUpload()
    {
      if (null === $this->file)
      {
        return;
      }

      $this->url = $this->file->guessExtension();

      $this->alt = $this->file->getClientOriginalName();
    }


    /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
    public function upload()
    {
      if (null === $this->file)
      {
        return;
      }

      // si on avait un ancien fichier, on le supprime
      if (null !== $this->tempName)
      {
        $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempName;
        if (file_exists($oldFile))
        {
          unlink($oldFile);
        }
      }

      $this->file->move(
        $this->getUploadRootDir(),
        $this->id.'.'.$this->url
      );
    }

    public function getUploadDir()
    {
      return 'uploads/img';
    }

    public function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
    * @ORM\PreRemove()
    */
    public function preRemoveUpload()
    {
      $this->tempName = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
    * @ORM\PostRemove()
    */
    public function removeUpload()
    {
      if (file_exists($this->tempName))
      {
        unlink($this->tempName);
      }
    }

    public function getWebPath()
    {
      return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
    }
}
