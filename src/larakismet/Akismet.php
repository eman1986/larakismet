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

    private static $_apiUrl = 'rest.akismet.com';
    private static $_apiKey;
    private static $_blog;
    private static $_commentType;
    private static $_commentAuthor;
    private static $_commentAuthorEmail;
    private static $_commentAuthorUrl;
    private static $_commentContent;
    private static $_commentDate;
    private static $_commentPostModified;
    private static $_language = array('en');
    private static $_charset = 'UTF-8';
    private static $_userRole;
    private static $_isTest;


    public function __construct($config) {
        $this->$_apiKey = $config['akismet_api_key'];
        $this->$_blog = $config['akismet_blog_url'];
        $this->$_isTest = $config['debug_mode'];
        //check to see if the key is valid.
        if (!self::ValidateKey()) {
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
    public static function setCommentType($type) {
        self::$_commentType = $type;
    }

    /**
     * Set Comment Author
     * @param string $author
     */
    public static function setCommentAuthor($author) {
        self::$_commentAuthor = $author;
    }

    /**
     * Set Comment Author Email
     * @param string $email
     */
    public static function setCommentAuthorEmail($email) {
        self::$_commentAuthorEmail = $email;
    }

    /**
     * Set Comment Author Url
     * @param string $url
     */
    public static function setCommentAuthorUrl($url) {
        self::$_commentAuthorUrl = $url;
    }

    /**
     * Set Comment Content
     * @param string $content
     */
    public static function setCommentContent($content) {
        self::$_commentContent = $content;
    }

    /**
     * Set Comment Date
     * @param DateTime $date
     */
    public static function setCommentDate($date) {
        self::$_commentDate = $date;
    }

    /**
     * Set Comment Post Modified
     * @param DateTime $date
     */
    public static function setCommentPostModified($date) {
        self::$_commentPostModified = $date;
    }

    /**
     * Set Blog Language(s)
     * @param array $lang
     */
    public static function setLanguage($lang) {
        self::$_language = $lang;
    }

    /**
     * Set Charset
     * @param string $charset
     */
    public static function setCharset($charset) {
        self::$_charset = $charset;
    }

    /**
     * Set User Role
     * @param string $role
     */
    public static function setUserRole($role) {
        self::$_userRole = $role;
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
    private static function validateKey() {
        try {
            $data = [
                "key" => self::$_apiKey,
                'blog' => urlencode(self::$_blog)
            ];
            $response = Request::post(self::$_apiUrl . '/1.1/verify-key')
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
    public static function checkSpam() {
        try {
            $data = [
                'blog' => urlencode(self::$_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => self::$_commentType,
                'comment_author' => self::$_commentAuthor,
                'comment_author_email' => self::$_commentAuthorEmail,
                'comment_author_url' => self::$_commentAuthorUrl,
                'comment_content' => self::$_commentContent,
                'comment_date_gmt' => self::$_commentDate,
                'comment_post_modified_gmt' => self::$_commentPostModified,
                'blog_lang' => self::$_language,
                'blog_charset' => self::$_charset,
                'user_role' => self::$_userRole,
                'is_test' => self::$_isTest
            ];
            $response = Request::post(sprintf('%s.%s/1.1/comment-check', self::$_apiUrl, self::$_apiKey))
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
    public static function reportSpam() {
        try {
            $data = [
                'blog' => urlencode(self::$_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => self::$_commentType,
                'comment_author' => self::$_commentAuthor,
                'comment_author_email' => self::$_commentAuthorEmail,
                'comment_author_url' => self::$_commentAuthorUrl,
                'comment_content' => self::$_commentContent
            ];
            $response = Request::post(sprintf('%s.%s/1.1/submit-spam', self::$_apiUrl, self::$_apiKey))
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
    public static function reportHam() {
        try {
            $data = [
                'blog' => urlencode(self::$_blog),
                'user_ip' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'],
                'user_agent' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'],
                'referrer' => class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'],
                'permalink' => class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'],
                'comment_type' => self::$_commentType,
                'comment_author' => self::$_commentAuthor,
                'comment_author_email' => self::$_commentAuthorEmail,
                'comment_author_url' => self::$_commentAuthorUrl,
                'comment_content' => self::$_commentContent
            ];
            $response = Request::post(sprintf('%s.%s/1.1/submit-ham', self::$_apiUrl, self::$_apiKey))
                ->body(http_build_query($data), Mime::FORM)
                ->send();

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}