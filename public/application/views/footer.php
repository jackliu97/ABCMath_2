</body>
<script src="/js/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/js/mousetrap.min.js" type="text/javascript"></script>
<script src="/js/private/common.js" type="text/javascript"></script>
<?php 
//always force to update latest.
$version = '?v=' . (int)microtime(true);
if(isset($datatable)){
	echo '<script src="/js/jquery.dataTables.min.js" type="text/javascript"></script>';
}

if(isset($handsontable)){
    echo '<script src="/js/jquery.handsontable.full.min.js" type="text/javascript"></script>';
}

if(isset($private_js)){

	foreach($private_js as $js){
		echo "<script src='/js/private/{$js}{$version}' type='text/javascript'></script>";
	}

}
?>
</body>
</html>