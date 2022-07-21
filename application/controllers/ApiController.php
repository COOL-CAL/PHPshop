<?php
    namespace application\controllers;

    use Exception;

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
            $param = [];

            if(isset($_GET["cate3"])) {
                $cate3 = intval($_GET["cate3"]);
                if($cate3 > 0) {
                    $param["cate3"] = $cate3;
                }
            } else {
                if(isset($_GET["cate1"])) {
                    $param["cate1"] = $_GET["cate1"];
                }
                if(isset($_GET["cate2"])) {
                    $param["cate2"] = $_GET["cate2"];
                }
            }                 
            return $this->model->productList($param);         
        }

        public function productList2() {
            return $this->model->productList2();
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
            if(count($urlPaths) !== 6) {
                exit();
            }
            $result = 0;
            switch(getMethod()) {
                case _DELETE:
                    //이미지 파일 삭제!
                    $product_image_id = intval($urlPaths[2]);
                    $product_id = intval($urlPaths[3]);
                    $type = intval($urlPaths[4]);
                    $path = $urlPaths[5];
    
                    $imgPath = _IMG_PATH . "/" . $product_id . "/" . $type . "/" . $path;
                    if(unlink($imgPath)) {
                        $param = [ "product_image_id" => $product_image_id ];
                        $result = $this->model->productImageDelete($param);
                    }
                    break;
            }
    
            return [_RESULT => $result];
        }
    
        public function deleteProduct() {
            $urlPaths = getUrlPaths();
            if(count($urlPaths) !== 3) {
                exit();
            }
            $productId = intval($urlPaths[2]);
            
            
            try {
                $param = [
                    "product_id" => $productId
                ];
                $this->model->beginTransaction();
                $this->model->productImageDelete($param);
                $result = $this->model->productDelete($param);
                if($result === 1) {
                    //이미지 삭제
                    rmdirAll(_IMG_PATH . "/" . $productId);
    
                    $this->model->commit();
                } else {
                    $this->model->rollback();    
                }
            } catch(Exception $e) {            
                $this->model->rollback();
            }    
            
            return [_RESULT => 1];
        }

        public function cate1List() {
            return $this->model->cate1List();
        }

        public function cate2List() {
            $urlPaths = getUrlPaths();
            if(count($urlPaths) !== 3) {
                exit();
            }        
            $param = [ "cate1" => $urlPaths[2] ];
            return $this->model->cate2List($param);
        }
    
        public function cate3List() {
            $urlPaths = getUrlPaths();
            if(count($urlPaths) !== 4) {
                exit();
            }        
            $param = [ 
                "cate1" => $urlPaths[2], 
                "cate2" => $urlPaths[3]
            ];
            return $this->model->cate3List($param);
        }
    }