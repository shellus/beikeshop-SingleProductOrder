<?php
namespace Plugin\SingleProductOrder;


use Beike\Models\Product;

class Bootstrap
{
    public function boot()
    {
        $this->beforeOrderPay();
    }

    public function beforeOrderPay()
    {
        // 后台编辑商品表单，渲染自定义字段
        add_hook_blade('admin.product.edit.extra', function ($callback, $output, $data) {
            $view = view('SingleProductOrder::admin.product.edit_extra_field', $data)->render();
            return $output . $view;
        }, 1);

        // 后台保存接口，保存自定义字段
        add_hook_action('admin.product.update.after', function ($data) {
            $product = $data['product'];
            $requestData = $data['request_data'];

            $product->is_single = $requestData['is_single'] ?? 0;
            $product->save();
        });

        // 前台结算页面，检查商品是否重复，以及数量只可以为1
        add_hook_filter('checkout.index.data', function ($data) {
            // $data: ["current","shipping_require","country_id","customer_id","countries","addresses","shipping_methods","payment_methods","carts","totals"]
            $carts = $data['carts'];
            // $carts: ["carts","quantity","quantity_all","amount","amount_format"]
            $products = $carts['carts'];
            // $products[0]: name variant_labels subtotal_format product_id
            foreach ($products as $cProduct) {
                $product = Product::findOrFail($cProduct['product_id']);
                if ($product->is_single && count($products) > 1) {
                    throw new \Exception(sprintf('商品【%s】不可以和其他商品一起结算', $cProduct['name']));
                }
                if ($product->is_single && $cProduct['quantity'] > 1) {
                    throw new \Exception(sprintf('商品【%s】一次只可以购买数量 1', $cProduct['name']));
                }
            }
            return $data;
        });
    }
}
