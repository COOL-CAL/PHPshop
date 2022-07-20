<?php
    namespace application\controllers;

    class ApiController extends Controller {
        public function categoryList() {
            return $this->model->getCategoryList();
        }

        public function productInsert() {
            $json = getJson();
            // print_r($json);
            return [_RESULT => $this->model->productInsert($json)];
        }

        public function productList() {
            $result = $this->model->productList();
            return $result === false ? [] : $result;
        }

        public function productDetail() {
            $urlPaths = getUrlPaths();
            if(!isset($urlPaths[2])) {
                exit();
            }
            $param = [
                'product_id' => intval($urlPaths[2])
            ];
            return $this->model->productDetail($param);
        }
        public function upload() {
            $urlPaths = getUrlPaths();
            if(!isset($urlPaths[2]) || !isset($urlPaths[3])) {
                exit();
            }
            $productId = intval($urlPaths[2]);
            $type = intval($urlPaths[3]);
            $json = getJson();
            $image_parts = explode(";base64,", $json["image"]);
            $image_type_aux = explode("image/", $image_parts[0]);      
            $image_type = $image_type_aux[1];      
            $image_base64 = base64_decode($image_parts[1]);
            $dirPath = _IMG_PATH . "/" . $productId . "/" . $type; //이미지 파일이 폴더에 저장되는 경로
            $nmPath = uniqid() . "." . $image_type; //랜덤 파일명.확장자 경로(이미지 경로)
            $filePath = $dirPath . "/" . $nmPath; //저장 경로/이미지 경로 (이것만 따로 저장함)
            if(!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
            $param = [ //param 안에 db에 들어갈 값 넣어줌
                'product_id' => $productId,
                'type' => $type,
                'path' => $nmPath
            ];
            //$file = _IMG_PATH . "/" . $productId . "/" . $type . "/" . uniqid() . "." . $image_type;
            //$file = "static/" . uniqid() . "." . $image_type;
            $result = file_put_contents($filePath, $image_base64); //이미지 파일을 폴더에 저장된다는 결과값
            return [_RESULT => $result ? $this->model->productImageInsert($param) : 0];
            //이미지가 폴더에 저장 되면 db에 값을 넣어주고, 없으면 just return
        }

        public function productImageList() {
            $urlPaths = getUrlPaths();
            if(!isset($urlPaths[2])) {
                exit();
            }
            $productId = intval($urlPaths[2]);
            $param = [
                "product_id" => $productId
            ];
            return $this->model->productImageList($param);
        }

        public function productImageDelete() {
            $urlPaths = getUrlPaths();
            if(!isset($urlPaths[5])) {
                exit();
            }
            $result = 0;
            switch(getMethod()) {
                case _DELETE:
                    unlink(_IMG_PATH . "/" . $urlPaths[2] . "/" . $urlPaths[3] . "/" . $urlPaths[4]);
                    $param = ["product_image_id" => intval($urlPaths[5])];
                    $result = $this->model->productImageDelete($param);
                    break;
                    
            }
            return [_RESULT => $result];
        }
    }