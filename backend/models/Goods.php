<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord{
    public function rules()
    {
        return [
            [['name','logo','goods_category_id','brand_id','market_price','shop_price','stock','is_on_sale','status','sort'],'required'],
            [['market_price','shop_price','stock',],'double']
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'商品名',
            'logo'=>'LOGO图片',
            'goods_category_id'=>'商品分类',
            'brand_id'=>'品牌分类',
            'market_price'=>'市场价格',
            'shop_price'=>'商品价格',
            'stock'=>'库存',
            'is_on_sale'=>'是否在销',
            'status'=>'状态',
            'sort'=>'排序',
            'create_time'=>'添加时间',
            'view_times'=>'浏览次数',
        ];
    }
    public static function Goods(){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $html=$redis->get('goods_Category');
        if($html==false){
            $category1=GoodsCategory::find()->where(['depth'=>0])->all();//一级分类
            foreach($category1 as $k1=>$category){
                $html.= '<div class="cat '.($k1?'':'item1').'">';
                $html.='<h3><a href="'.\yii\helpers\Url::to(['list/index','id'=>$category->id]).'">'.$category->name.'</a> <b></b></h3>';
                $html.= '<div class="cat_detail">';
                $result[$category->id]=GoodsCategory::find()->where(['parent_id'=>$category->id])->all();
                foreach($result[$category->id] as $k2=>$cate){
                    $html.='<dl'.($k2?'':'class="dl_1st"').'>';
                    $html.= '<dt><a href="'.\yii\helpers\Url::to(['list/index','id'=>$cate->id]).'">'.$cate->name.'</a></dt>';
                    $re[$cate->id]=GoodsCategory::find()->where(['parent_id'=>$cate->id])->all();
                    foreach($re[$cate->id] as $ca){
                        $html.='<dd>';
                        $html.='<a href="'.\yii\helpers\Url::to(['list/index','id'=>$ca->id]).'">'.$ca->name.'</a>';
                        $html.='</dd>';
                    }
                    $html.='</dl>';
                }
                $html.=' </div>';
                $html.=' </div>';
            }
            $redis->set('goods_Category',$html,24*3600);
        }
        return $html;
    }
}