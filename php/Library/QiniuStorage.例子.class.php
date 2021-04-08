<?php
	namespace Library;

	class QiniuStorageExample {
        /**
         * 七牛上传图片
         */
        protected function picSave($pic){
            $data2= base64_decode($pic);
            $ext = $this->checkSuffix($data2);
            if(!$ext){
                echo '图片类型不正确！';
            }
            $config =  array (
                'savePath'         => "assets/sherpaimg/",
                'driverConfig'     => array (
                    'secretKey' => 'x5YRVof6ZywdR2CefMhjImjr3HwNxMuyqCO1_yXm',
                    'accessKey' => 'k0KmawQ6k5pwECC3EhFpjN3QwnymsGpK4J76TTI2',
                    'domain'    => 'img.sherpa.com.cn',
                    'bucket'    => 'sherpaimg',
                    'timeout'   => 300,
                )
            );
            $qiniu = new QiniuStorage($config['driverConfig']);
            $upfile = array(
                'name'=>'file',
                'fileName'=>$config['savePath'].'ApiShipping/' .md5(time().uniqid(mt_rand(), true)).'.'.$ext,
                'fileBody'=>$data2
            );
            $result = $qiniu->upload([], $upfile);
            return $result;
        }

        //判断图片后缀
        protected function checkSuffix($file){
            $suffixCode = bin2hex(substr($file, 0, 4));
            $suffixData = [
                '474946383961' => 'gif',
                'ffd8ffe0' => 'jpg',
                'ffd8ffe1' => 'jpg',
                'ffd8ffe8' => 'jpg',
                '89504e470d0a1a0a' => 'png'
            ];
            if(isset($suffixData[$suffixCode])){
                $ext = $suffixData[$suffixCode];
            }else{
                return false;
            }
            return $ext;
        }
	}
