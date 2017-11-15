<?php

namespace app\modules\m\controllers;

use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;
use app\models\book\Book;
use app\models\member\MemberCart;
use app\models\member\MemberFav;
use app\common\services\UrlService;
use app\common\services\UtilService;
use app\common\services\ConstantMapService;
class ProductController extends BaseController
{
	/*
		商品列表
	 */
    public function actionIndex()
    {
        $kw = trim( $this->get("kw","") );
        $sort_field = trim( $this->get("sort_field","default") );
        $sort = trim( $this->get("sort","") );
        $sort = in_array(  $sort,['asc','desc'] )?$sort:'desc';

        $list = $this->getSearchData( );
        $data = [];
        if( $list ){
            foreach( $list as $_item ){
                $data[] = [
                    'id' => $_item['id'],
                    'name' => UtilService::encode( $_item['name'] ),
                    'price' => UtilService::encode( $_item['price'] ),
                    'main_image_url' => UrlService::buildPicUrl("book",$_item['main_image'] ),
                    'month_count' => $_item['month_count']
                ];
            }
        }

        $search_conditions = [
            'kw' => $kw,
            'sort_field' => $sort_field,
            'sort' => $sort
        ];

        return $this->render("index",[
            'list' => $data,
            'search_conditions' => $search_conditions
        ]);
    }
    /*
    	商品详情
     */
    public function actionInfo()
    {
        $id = intval( $this->get("id",0) );
        $reback_url = UrlService::buildMUrl("/product/index");
        if( !$id ){
            return $this->redirect( $reback_url );
        }

        $info = Book::findOne([ 'id' => $id ]);//$list=Book::find()->where(['id'=>$id])->asArray()->one();//one()将查询结果转为一维的
        if( !$info ){
            return $this->redirect( $reback_url );
        }

        $has_faved = false;
        if(  $this->current_user ){
            $has_faved = MemberFav::find()->where([ 'member_id' => $this->current_user['id'],'book_id' => $id ])->count();//current_user['id']为用户id
        }
        return $this->render('info',['list'=>$info,'has_faved' => $has_faved]);
    }
    /*
        收藏
     */
    public function actionFav(){
        $act = trim( $this->post("act","") );
        $book_id = intval( $this->post("book_id",0) );

        if( $act == "del" ){
        }


        if( !$book_id ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $has_faved = MemberFav::find()->where([ 'member_id' => $this->current_user['id'],'book_id' => $book_id ])->count();
        if( $has_faved ){
            return $this->renderJSON( [],"已收藏~~",-1 );
        }

        $model_fav = new MemberFav();
        $model_fav->member_id = $this->current_user['id'];
        $model_fav->book_id = $book_id;
        $model_fav->created_time = date("Y-m-d H:i:s");
        $model_fav->save( 0 );
        return $this->renderJSON( [],"收藏成功~~" );
    }
    /*
        购物车
     */
    public function actionCart()
    {
        $act = trim( $this->post("act","") );
        $book_id = intval( $this->post("book_id",0) );
        $quantity = intval( $this->post("quantity",0) );
        $date_now = date("Y-m-d H:i:s");

        if( !in_array( $act,[ "del","set" ] ) ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        if( !$book_id || !$quantity ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $book_info = Book::findOne([ 'id' => $book_id ]);
        if( !$book_info ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $cart_info = MemberCart::find()->where([ 'member_id' => $this->current_user['id'],'book_id' => $book_id ])->one();
        if( $cart_info  ){
            $model_cart = $cart_info;
        }else{
            $model_cart = new MemberCart();
            $model_cart->member_id = $this->current_user['id'];
            $model_cart->created_time = $date_now;
        }

            $model_cart->book_id = $book_id;
            $model_cart->quantity = $quantity;
            $model_cart->updated_time = $date_now;
            $model_cart->save ( 0 );

        return $this->renderJSON( [],"操作成功~~" );
    }
    /*
     * 操作
     */
    public function actionOps()
    {
        $act = trim( $this->post("act","") );
        $book_id = intval( $this->post("book_id",0) );
        $book_info = Book::findOne([ 'id' => $book_id ]);
        if( !$book_info ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }
        $book_info->view_count += 1; //浏览数字段加一
        $book_info->update( 0 );
        return $this->renderJson( [] );
    }

    /*
     *下单页面
     */
    public function actionOrder()
    {
        return $this->render('order');
    }
    /*
     * 搜索方法
     */
    public function actionSearch(){
        $list = $this->getSearchData( );
        $data = [];
        if( $list ){
            foreach( $list as $_item ){
                $data[] = [
                    'id' => $_item['id'],
                    'name' => UtilService::encode( $_item['name'] ),
                    'price' => UtilService::encode( $_item['price'] ),
                    'main_image_url' => UrlService::buildPicUrl("book",$_item['main_image'] ),
                    'month_count' => $_item['month_count']
                ];
            }
        }
//        echo '<pre/>';
//        print_r($list);die;
        return $this->renderJson( [ 'data' => $data ,'has_next' => ( count( $data ) == 4 )?1:0 ] );
    }
    /*
     * 封装的搜索方法
     */
    private function getSearchData( $page_size = 4  ){
        $kw = trim( $this->get("kw","") );
        $sort_field = trim( $this->get("sort_field","default") );
        $sort = trim( $this->get("sort","") );
        $sort = in_array(  $sort,['asc','desc'] )?$sort:'desc';
        $p = intval( $this->get("p",1 ) );
        if( $p < 1 ){
            $p = 1;
        }

        $query = Book::find()->where([ 'status' => 1 ]);
        if( $kw ){
            $where_name = [ 'LIKE','name','%'.strtr($kw,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
            $where_tags = [ 'LIKE','tags','%'.strtr($kw,['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%', false ];
            $query->andWhere([ 'OR',$where_name,$where_tags ]);
        }

        switch ( $sort_field ){
            case "view_count":
            case "month_count":
            case "price":
                $query->orderBy( [  $sort_field => ( $sort == "asc")?SORT_ASC:SORT_DESC,'id' => SORT_DESC ] );
                break;
            default:
                $query->orderBy([ 'id' => SORT_DESC ]);
                break;
        }

        return $query->offset(  ( $p - 1 ) * $page_size )
            ->limit( $page_size )->asArray()
            ->all();
    }
}
