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

namespace Customize\Controller;

use Doctrine\DBAL\Connection;
use Eccube\Controller\AbstractController;
use Eccube\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SQLiDemoController extends AbstractController
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * SQLiDemoController constructor.
     *
     * @param \Doctrine\DBAL\Connection $conn
     * @param ProductRepository $productRepository
     */
    public function __construct(
        \Doctrine\DBAL\Connection $conn,
        ProductRepository $productRepository
    ) {
        $this->conn = $conn;
        $this->productRepository = $productRepository;
    }

    /**
     * SQLインジェクションデモページ.
     *
     * @Route("/sql_injection_demo", name="sql_injection_demo", methods={"GET", "POST"})
     * @Template("SQLiDemo/index.twig")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request)
    {
        $searchWord = $request->get('search_word', '');
        $searchType = $request->get('search_type', 'safe');
        $results = [];
        $error = null;

        if ($searchWord) {
            try {
                $results = $this->searchProducts($searchWord, $searchType);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return [
            'search_word' => $searchWord,
            'search_type' => $searchType,
            'results' => $results,
            'error' => $error,
        ];
    }

    /**
     * 商品検索を実行する
     * 
     * @param string $searchWord 検索キーワード
     * @param string $searchType 検索タイプ（safe/unsafe）
     * @return array 検索結果
     */
    private function searchProducts(string $searchWord, string $searchType): array
    {
        // 安全な検索方法（パラメータバインディングあり）
        if ($searchType === 'safe') {
            return $this->searchProductsSafely($searchWord);
        } 
        // 安全でない検索方法（SQLインジェクションの脆弱性あり）
        else if ($searchType === 'unsafe') {
            // 注意: このコードは実際には使用しないでください！教育目的のみです
            return $this->searchProductsUnsafely($searchWord);
        }
        
        return [];
    }
    
    /**
     * 安全な方法で商品を検索する
     * 
     * @param string $searchWord 検索キーワード
     * @return array 検索結果
     */
    private function searchProductsSafely(string $searchWord): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from('Eccube\\Entity\\Product', 'p')
            ->where('p.name LIKE :keyword')
            ->setParameter('keyword', '%'.$searchWord.'%')
            ->orderBy('p.id', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 安全でない方法で商品を検索する（教育目的のみ）
     * 
     * @param string $searchWord 検索キーワード
     * @return array 検索結果
     */
    private function searchProductsUnsafely(string $searchWord): array
    {
        // 注意: このコードは実際には使用しないでください！教育目的のみです
        $sql = "SELECT * FROM dtb_product WHERE name LIKE '%".$searchWord."%' ORDER BY id ASC";
        $stmt = $this->conn->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }
}