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
use app\common\services\PayOrderService;
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
        if( \Yii::$app->request->isGet ) {
            $book_id = intval($this->get("id", 0));
            $quantity = intval($this->get("quantity", 1));
//            $sc = $this->get("sc", "product");//sc source 来源
            $product_list = [];
            $total_pay_money = 0;
            if( $book_id ){
                $book_info = Book::find()->where([ 'id' => $book_id ])->one();
                if( $book_info ){
                    $product_list[] = [
                        'id' => $book_info['id'],
                        'name' => UtilService::encode( $book_info['name'] ),
                        'quantity' => $quantity,
                        'price' => $book_info['price'],
                        'main_image' =>  UrlService::buildPicUrl( "book",$book_info['main_image'])
                    ];
                    $total_pay_money += $book_info['price'] * $quantity;
                }
            }
            return $this->render("order",[
                'product_list' => $product_list,
                'total_pay_money' => sprintf("%.2f",$total_pay_money),
            ]);
        }
//        $sc = trim( $this->post("sc","") ); 地址
        $product_items = $this->post("product_items",[]);
        $address_id = intval( $this->post("address_id",0 ) );

//        if( !$address_id ){
//            return $this->renderJSON([],"请选择收货地址~~",-1);
//        }

        if( !$product_items ){
            return $this->renderJSON([],"请选择商品之后在提交~~",-1);
        }

        $book_ids = [];  //一件订单有多个商品，所以商品都放入一个数组中
        foreach( $product_items as $_item ) {
            $tmp_item_info = explode("#", $_item);  //$product_items除了 商品id外还有商品数量且以"#"分割
            $book_ids[] = $tmp_item_info[ 0 ];
        }

        $book_mapping = Book::find()->where([ 'id' => $book_ids ])->indexBy("id")->all();
        if( !$book_mapping ){
            return $this->renderJSON([],"请选择商品之后在提交~~",-1);
        }

        $target_type = 1;
        $items = [];
        foreach( $product_items as $_item ){
            $tmp_item_info = explode("#",$_item); //分割数组
            $tmp_book_info = $book_mapping[ $tmp_item_info[0] ];
            $items[] = [
                'price' => $tmp_book_info['price'] * $tmp_item_info[1],
                'quantity' => $tmp_item_info[1],
                'target_type' => $target_type,
                'target_id' => $tmp_item_info[0]
            ];
        }


        $params = [
            'pay_type' => 1,
            'pay_source' => 2,
            'target_type' => $target_type,
            'note' => '购买书籍',
            'status' => -8,
            'express_address_id' => $address_id
        ];


        $ret = PayOrderService::createPayOrder( $this->current_user['id'],$items,$params );

        if( !$ret ){
            return $this->renderJSON([],"提交失败，失败原因：".PayOrderService::getLastErrorMsg(),-1 );
        }

        return $this->renderJSON([ 'url' => UrlService::buildMUrl("/pay/buy/?pay_order_id={$ret['id']}") ],'下单成功,前去支付~~' );
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
