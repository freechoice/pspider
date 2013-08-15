<?php
/**
 * pspider - custom template file
 * 
 * @author hightman <hightman@twomice.net>
 * @link http://www.hightman.cn/
 * @copyright Copyright &copy; 2008-2013 Twomice Studio
 */
/// --- custom 并发抓取数量
define('PSP_NUM_PARALLEL', 5);

/// --- custom 同一 URL 连续抓取间隔
define('PSP_CRAWL_PERIOD', 3600);

/**
 * 设置 Postgres 参数，要求带有 _urls 表，并采用以下结构：
	-- ----------------------------
	--  Table structure for _urls
	-- ----------------------------
	DROP TABLE IF EXISTS "_urls";
	CREATE TABLE "_urls" (
		"id" varchar(32) NOT NULL COLLATE "default",
		"url" text COLLATE "default",
		"rank" int4,
		"status" int4,
		"select_time" int8,
		"update_time" int8
	)
	WITH (OIDS=FALSE);
	
	-- ----------------------------
	--  Primary key structure for table _urls
	-- ----------------------------
	ALTER TABLE "_urls" ADD CONSTRAINT "_urls_pkey" PRIMARY KEY ("id") NOT DEFERRABLE INITIALLY IMMEDIATE;
 */
class UrlTableCustom extends UrlTablePostgres
{

	public function __construct()
	{
		/// --- custom setting BEGIN
		$host = 'localhost';
		$user = 'postgres';
		$pass = '';
		$dbname = '';
		$port=  '';
		
		/// --- custom setting END
		$dsn = "pgsql:host=$host;";
		$dsn .= $port<>'' ? "port=$port;" : '';
		$dsn .= "dbname=$dbname;client_encoding=utf-8";
		
		parent::__construct($dsn, $user, $pass);
		$this->test();
	}
}

/**
 * 自定义解析器
 */
class UrlParserCustom extends UrlParser
{

	/**
	 * 在这个方法内添加抓取内容解析处理代码
	 */
	public function parse($res, $req, $key)
	{
		parent::parse($res, $req, $key,'callback');
		if ($res->status === 200)
		{
			/// --- custom code BEGIN ---
			echo "PROCESSING: " . $req->getUrl() . "\n";
			/// --- custom code END ---
		}
	}

	public function parsecallback($body) {
		/**
		 * @todo parse callback function
		 */
	}
	
	/**
	 * 在这个方法内添加新 URL 过滤规则，主要是调用以下方法：
	 * followExternal()
	 * allowDomain(), disallowDomain()
	 * allow(), disallow(), disallowExt()
	 */
	public function defaultFilter()
	{
		parent::defaultFilter();
		/// --- custom filter BEGIN ---
		$this->followExternal(false);
		$this->disallow('.php?q=');
		/// --- custom filter END ---
	}
}
