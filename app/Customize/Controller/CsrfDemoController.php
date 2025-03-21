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

use Eccube\Controller\AbstractController;
use Eccube\Common\Constant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfDemoController extends AbstractController
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * CsrfDemoController コンストラクタ
     * 
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * CSRFデモページ
     *
     * @Route("/csrf_demo", name="csrf_demo", methods={"GET", "POST"})
     * @Template("CsrfDemo/index.twig")
     * 
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $action = $request->get('action', '');
        // $protectionType = $request->get('protection_type', 'protected');
        $result = null;
        
        // CSRFトークンを生成
        $csrfToken = $this->tokenManager->getToken('csrf_demo')->getValue();
        
        if ($this->isValidFormSubmission($request, $action)) {
            $result = $this->processAction($action);
        }
        
        return [
            'csrf_token' => $csrfToken,
            // 'protection_type' => $protectionType,
            'action' => $action,
            'result' => $result,
        ];
    }
    
    /**
     * 悪意のあるサイトを模したデモページ
     * 
     * @Route("/csrf_evil_demo", name="csrf_evil_demo", methods={"GET"})
     * @Template("CsrfDemo/evil.twig")
     * 
     * @return array
     */
    public function evilSite()
    {
        return [];
    }
    
    /**
     * フォーム送信が有効かどうかを判定する
     * 
     * @param Request $request リクエスト
     * @param string $action アクション
     * @return bool 有効な場合はtrue
     */
    private function isValidFormSubmission(Request $request, string $action): bool
    {
        if (!$request->isMethod('POST') || empty($action)) {
            return false;
        }
        
        // if ($protectionType === 'protected') {
            return $this->isCsrfTokenValid('csrf_demo', $request->get('_token'));
        // }
        
        // return true;
    }
    
    /**
     * アクションを処理する
     * 
     * @param string $action 実行するアクション
     * @return array 処理結果
     */
    private function processAction(string $action): array
    {
        $messages = [
            'change_email' => 'メールアドレスが変更されました。',
            'transfer_money' => '送金が完了しました。',
            'delete_account' => 'アカウントが削除されました。',
        ];
        
        if (isset($messages[$action])) {
            return [
                'success' => true,
                'message' => $messages[$action],
                'action' => $action,
            ];
        }
        
        return [
            'success' => false,
            'message' => '不明なアクションです。',
            'action' => $action,
        ];
    }
}