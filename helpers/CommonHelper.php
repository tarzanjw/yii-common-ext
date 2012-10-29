<?php

class CommonHelper
{
    /**
    * Biến 1 mảng dữ liệu (nhiều cấp) thành 1 query string, các thành phần được
    * của mảng được sắp xếp theo key trước khi ghép thành query string
    * Query string trả về đã được encode theo url encode, chuẩn PHP_QUERY_RFC3986
    * (biến [space] thành %20 thay vì dấu +)
    *
    * Xem thêm {@link http://php.net/manual/en/function.http-build-query.php}
    * Hàm này được viết ra vì hàm trên (chuẩn của PHP) không build những tham số
    * có giá trị rỗng và không sort.
    *
    * @param array $data mảng dữ liệu 1+ cấp
    * @param array $queryStrings các phần tử của query string sẽ được ghép vào đây
    *
    * @return string query string
    */
    static function buildQueryString($data, $numericPrefix='', $argSeparator='&', $prefix=null)
    {
    	$keys = array_keys($data);
    	sort($keys, SORT_STRING);

    	$entries = array();

    	$queries = array();;

		foreach ($keys as $k) {
			$v = $data[$k];
			if (!empty($prefix)) $k = $prefix.'['.$k.']';
			if (is_array($v)) $queries[] = self::buildQueryString($v, $numericPrefix, $argSeparator, $k);
			else $queries[] = rawurlencode($k).'='.rawurlencode($v);
		}

		return implode($argSeparator, $queries);
    }
}