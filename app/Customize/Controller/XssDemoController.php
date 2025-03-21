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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XssDemoController extends AbstractController
{
    /**
     * クロスサイトスクリプティングデモページ.
     *
     * @Route("/xss_demo", name="xss_demo", methods={"GET", "POST"})
     * @Template("XssDemo/index.twig")
     */
    public function index(Request $request)
    {
        $message = $request->get('message', '');
        $displayType = $request->get('display_type', 'safe');

        return [
            'message' => $message,
            'display_type' => $displayType,
            'rendered_message' => $this->renderMessage($message, $displayType),
        ];
    }

    /**
     * メッセージを表示用に処理する
     * 
     * @param string $message 入力メッセージ
     * @param string $displayType 表示タイプ（safe/unsafe）
     * @return string 処理されたメッセージ
     */
    private function renderMessage(string $message, string $displayType): string
    {
        if (empty($message)) {
            return '';
        }

        // return match ($displayType) {
        //     'safe' => $this->renderMessageSafely($message),
        //     'unsafe' => $this->renderMessageUnsafely($message),
        //     default => '',
        // };

        // Twig側でエスケープしているので、ここでは何もしない
        return $message;
    }
    
    /**
     * 安全な方法でメッセージを表示する
     * 
     * @param string $message 入力メッセージ
     * @return string 安全に処理されたメッセージ
     */
    private function renderMessageSafely(string $message): string
    {
        // HTMLエスケープして安全に表示
        return htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * 安全でない方法でメッセージを表示する（教育目的のみ）
     * 
     * @param string $message 入力メッセージ
     * @return string エスケープされていないメッセージ
     */
    private function renderMessageUnsafely(string $message): string
    {
        // 注意: このコードは実際には使用しないでください！教育目的のみです
        // HTMLをエスケープせずに直接出力
        return $message;
    }
}