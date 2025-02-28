<?php
// 세션 시작
session_start();
 
// 세션 변수 초기화
$_SESSION = array();
 
// 세션 제거
session_destroy();
 
// 로그인 페이지로 리디렉션
header("location: login.php");
exit;
?> 