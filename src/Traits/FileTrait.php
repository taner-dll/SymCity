<?php
/**
 * Created by PhpStorm.
 * User: tnrdll
 * Date: 3/25/19
 * Time: 4:26 AM
 */

namespace App\Traits;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;

trait FileTrait
{



    /**
     *
     * @param $file
     * @param string $prefix
     * @param $dir
     * @param $fileOldName
     * @param $em
     * @param $entity FileTrait veritabanında ilgili alanı günceller
     *
     * @return JsonResponse
     * @author Taner Deliloğlu <tnrdll@gmail.com>
     */
    public function base64upload($file, string $prefix, $dir, $fileOldName, $em, $entity)
    {
        $fs = new Filesystem();

        //base64 içerisinden dosya tipini bul
        $pos = strpos($file, ';');
        $type = explode(':', substr($file, 0, $pos))[1]; // image/png
        $ext = explode('/', $type)[1]; // png, jpg...
        $fileName = date('Ymd') . uniqid($prefix, false) . '.' . $ext;


        //kendisine ait önceki resmi kontrol et ve sil
        if (strlen($fileOldName) > 0 && is_file($dir . '/' . $fileOldName)) {
            $fs->remove($dir . '/' . $fileOldName);
        }


        if (is_object($entity)){
            $entity->setFeaturedPicture($fileName);
            $em->flush();
        }



        // base64 cözümle.
        $base64_string = str_replace('data:' . $type . ';base64,', '', $file);
        $base64_string = str_replace(' ', '+', $base64_string);

        $file = base64_decode($base64_string);
        $fs->exists($dir) ? null : $fs->mkdir($dir); // dir yoksa yarat.

        if ($fs->exists($dir)) {
            try {

                //base64 string move_uploaded_file ile çalışmaz.
                //file_put_contents ile dosya oluşturulmalıdır.
                //move_uploaded_file($file, $dir);
                file_put_contents($dir . '/' . $fileName, $file);

            } catch (FileException $e) {
                return new JsonResponse($e->getMessage());
            }
        }
    }


    /**
     * @param $dir
     * @param $fileName
     * @return JsonResponse
     */
    public function removeFeaturedPicture($dir, $fileName)
    {
        $fs = new Filesystem();
        try{
            if (strlen($fileName) > 0 && is_file($dir . '/' . $fileName)) {
                $fs->remove($dir . '/' . $fileName);
            }

        }catch (FileException $e){
            return new JsonResponse($e->getMessage());
        }
    }


}