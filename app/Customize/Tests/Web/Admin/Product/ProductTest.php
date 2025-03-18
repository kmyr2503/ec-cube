<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Tests\Web\Admin\Product;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
// use Symfony\Component\DomCrawler\Crawler;

/**
 * 商品登録時の必須項目チェックのテスト
 */
class ProductTest extends AbstractAdminWebTestCase
{
    /**
     * 商品登録の基本情報フォームを取得
     *
     * @return array
     */
    private function createFormData()
    {
        return [
            'class' => [
                'sale_type' => 1,
                'price01' => 1000,
                'price02' => 2000,
                'stock' => 100,
                // 'stock_unlimited' => 0, // オフの場合は要素そのものが送られない
                'code' => 'code',
                'sale_limit' => null,
                'delivery_duration' => '',
            ],
            'name' => '商品名',
            'product_image' => [],
            'description_detail' => '商品説明',
            'description_list' => '商品説明(一覧)',
            'Category' => [],
            'Tag' => [1],
            'search_word' => '検索ワード',
            'free_area' => 'フリーエリア',
            'Status' => 1,
            'note' => 'ノート',
            'tags' => [],
            'images' => [],
            'add_images' => [],
            'delete_images' => [],
            'return_link' => $this->generateUrl('admin_product'),
        ];
    }

    /**
     * 商品名が空欄の場合のテスト
     */
    public function testValidationWithEmptyName()
    {
        $formData = $this->createFormData();
        $formData['name'] = '';  // 商品名を空に設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // 入力エラーがあることを確認
        $this->assertTrue($crawler->filter('#admin_product_name')->closest('.row')->filter('.form-error-message')->count() > 0);
        
        // 商品名のエラーメッセージを確認
        $errorMessage = $crawler->filter('#admin_product_name')->closest('.row')->filter('.form-error-message')->text();
        $this->assertStringContainsString('入力されていません', $errorMessage);
    }

    /**
     * 販売価格が空欄の場合のテスト
     */
    public function testValidationWithEmptyPrice()
    {
        $formData = $this->createFormData();
        $formData['class']['price02'] = ''; // 販売価格を空に設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // 入力エラーがあることを確認
        $this->assertTrue($crawler->filter('#admin_product_class_price02')->closest('.row')->filter('.form-error-message')->count() > 0);
        
        // 販売価格のエラーメッセージを確認
        $errorMessage = $crawler->filter('#admin_product_class_price02')->closest('.row')->filter('.form-error-message')->text();
        $this->assertStringContainsString('入力されていません。', $errorMessage);
    }

    /**
     * 販売価格が負数の場合のテスト
     */
    public function testValidationWithNegativePrice()
    {
        $formData = $this->createFormData();
        $formData['class']['price02'] = -100; // 販売価格を負数に設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // 入力エラーがあることを確認
        $this->assertTrue($crawler->filter('#admin_product_class_price02')->closest('.row')->filter('.form-error-message')->count() > 0);
        
        // 販売価格のエラーメッセージを確認
        $errorMessage = $crawler->filter('#admin_product_class_price02')->closest('.row')->filter('.form-error-message')->text();
        $this->assertStringContainsString('0以上2147483647以下でなければなりません。', $errorMessage);
    }

    /**
     * 在庫数が空欄で在庫無制限でない場合のテスト
     */
    public function testValidationWithEmptyStock()
    {
        $formData = $this->createFormData();
        $formData['class']['stock'] = ''; // 在庫数を空に設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // 入力エラーがあることを確認
        $this->assertTrue($crawler->filter('#admin_product_class_stock')->closest('.row')->filter('.form-error-message')->count() > 0);
        
        // 在庫数のエラーメッセージを確認
        $errorMessage = $crawler->filter('#admin_product_class_stock')->closest('.row')->filter('.form-error-message')->text();
        $this->assertStringContainsString('在庫数を入力、もしくは在庫無制限を設定してください。', $errorMessage);
    }

    /**
     * 在庫数が負数の場合のテスト
     */
    public function testValidationWithNegativeStock()
    {
        $formData = $this->createFormData();
        $formData['class']['stock'] = -100; // 在庫数を負数に設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // 入力エラーがあることを確認
        $this->assertTrue($crawler->filter('#admin_product_class_stock')->closest('.row')->filter('.form-error-message')->count() > 0);
        
        // 在庫数のエラーメッセージを確認
        $errorMessage = $crawler->filter('#admin_product_class_stock')->closest('.row')->filter('.form-error-message')->text();
        $this->assertStringContainsString('数字で入力してください。', $errorMessage);
    }

    /**
     * 正常な入力値での登録成功テスト
     */
    public function testValidationSuccess()
    {
        $formData = $this->createFormData();

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );
        
        // ステータスコードが200であることを確認
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * 在庫無制限の場合のテスト
     */
    public function testValidationWithStockUnlimited()
    {
        $formData = $this->createFormData();
        $formData['class']['stock_unlimited'] = 1; // 在庫無制限をオンに設定

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // ステータスコードが200であることを確認
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
} 