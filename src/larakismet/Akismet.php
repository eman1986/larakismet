<?php
/**
 * Larakismet
 *
 * Akismet Client for Laravel 5.
 *
 * Ed Lomonaco
 * https://github.com/eman1986/larakismet
 * MIT License
 */

namespace larakismet;

use Httpful\Mime;
use Httpful\Request;

class Akismet {

    private $_apiUrl = 'rest.akismet.com';
    private $_apiKey;
    private $_blog;
    private $_commentType;
    private $_commentAuthor;
    private $_commentAuthorEmail;
    private $_commentAuthorUrl;
    private $_commentContent;
    private $_commentDate;
    private $_commentPostModified;
    private $_language = array('en');
    private $_charset = 'UTF-8';
    private $_userRole;
    private $_isTest;

    public function __construct($config) {
        $this->_apiKey = $config['akismet_api_key'];
        $this->_blog = $config['akismet_blog_url'];
        $this->_isTest = $config['debug_mode'];
        //check to see if the key is valid.
        if (!$this->ValidateKey()) {
            throw new \Exception('Akismet API Key is invalid. You can obtain a valid one from https://akismet.com');
        }
    }

    /**
     * PROPERTIES
     */

    /**
     * Set Comment Type
     * @param string $type
     */
    public function setCommentType($type) {
       $this->_commentType = $type;
    }

    /**
     * Set Comment Author
     * @param string $author
     */
    public function setCommentAuthor($author) {
       $this->_commentAuthor = $author;
    }

    /**
     * Set Comment Author Email
     * @param string $email
     */
    public function setCommentAuthorEmail($email) {
       $this->_commentAuthorEmail = $email;
    }

    /**
     * Set Comment Author Url
     * @param string $url
     */
    public function setCommentAuthorUrl($url) {
       $this->_commentAuthorUrl = $url;
    }

    /**
     * Set Comment Content
     * @param string $content
     */
    public function setCommentContent($content) {
       $this->_commentContent = $content;
    }

    /**
     * Set Comment Date
     * @param DateTime $date
     */
    public function setCommentDate($date) {
       $this->_commentDate = $date;
    }

    /**
     * Set Comment Post Modified
     * @param DateTime $date
     */
    public function setCommentPostModified($date) {
       $this->_commentPostModified = $date;
    }

    /**
     * Set Blog Language(s)
     * @param array $lang
     */
    public function setLanguage($lang) {
       $this->_language = $lang;
    }

    /**
     * Set Charset
     * @param string $charset
     */
    public function setCharset($charset) {
       $this->_charset = $charset;
    }

    /**
     * Set User Role
     * @param string $role
     */
    public function setUserRole($role) {
       $this->_userRole = $role;
    }

    /**
     * METHODS
     */

    /**
     * See if the API Key is valid.
     * @return mixed
     * @throws \Exception
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    private function validateKey() {
        try {
            $data = [
                "key" => $this->_apiKey,
                'blog' => urlencode($this->_blog)
            ];
            $response = Request::post($this->_apiUrl . '/1.1/verify-key')
                ->body(http_build_query($data), Mime::FORM)
                ->send();

            return $response == 'valid';
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Check to see if a comment has spam contents.
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function checkSpam() {
        try {
            $data = [
                'blog' => urlencode($this->_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => $this->_commentType,
                'comment_author' => $this->_commentAuthor,
                'comment_author_email' => $this->_commentAuthorEmail,
                'comment_author_url' => $this->_commentAuthorUrl,
                'comment_content' => $this->_commentContent,
                'comment_date_gmt' => $this->_commentDate,
                'comment_post_modified_gmt' => $this->_commentPostModified,
                'blog_lang' => $this->_language,
                'blog_charset' => $this->_charset,
                'user_role' => $this->_userRole,
                'is_test' => $this->_isTest
            ];
            $response = Request::post(sprintf('%s.%s/1.1/comment-check',$this->_apiKey, $this->_apiUrl))
                ->body(http_build_query($data), Mime::FORM)
                ->send();

            return ((bool)$response == false);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Submit spam sample to Akismet.
     * @return mixed
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function reportSpam() {
        try {
            $data = [
                'blog' => urlencode($this->_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => $this->_commentType,
                'comment_author' => $this->_commentAuthor,
                'comment_author_email' => $this->_commentAuthorEmail,
                'comment_author_url' => $this->_commentAuthorUrl,
                'comment_content' => $this->_commentContent
            ];
            $response = Request::post(sprintf('%s.%s/1.1/submit-spam',$this->_apiKey, $this->_apiUrl))
                ->body(http_build_query($data), Mime::FORM)
                ->send();

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Report a false positive to Akismet.
     * @return mixed
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function reportHam() {
        try {
            $data = [
                'blog' => urlencode($this->_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() :$_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') :$_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() :$_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => $this->_commentType,
                'comment_author' => $this->_commentAuthor,
                'comment_author_email' => $this->_commentAuthorEmail,
                'comment_author_url' => $this->_commentAuthorUrl,
                'comment_content' => $this->_commentContent
            ];
            $response = Request::post(sprintf('%s.%s/1.1/submit-ham',$this->_apiKey, $this->_apiUrl))
                ->body(http_build_query($data), Mime::FORM)
                ->send();

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}