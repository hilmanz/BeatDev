<div id="header">
	<div class="container">	
    	<div class="entry1">		
            <h1><a href="index.php" class="logo">&nbsp;</a></h1>			
            <div id="info-user">
                <div class="info-details">
                    <h4><a href="#">Hi, Darth Father</a> <a class="icon_flag" href="#">121</a><a style="padding-left:40px" href="#">Log Out</a></h4>
                </div> <!-- /.info-details -->
            </div> <!-- /#info-user -->
		</div> <!-- /.entry -->
	</div> <!-- /.container -->
</div> <!-- /#header -->
<div id="menu-relative">
    <div id="main-menu-dashboard">
        <ul id="nav_dropdown" class="mainNav nav nav-pills">
            <li><a id="blue_menu" href="index.php?menu=home">PERFORMANCE</a></li>
            <li><a href="index.php?menu=ba-rank">BA RANK</a></li>
            <li><a href="index.php?menu=web-activities">WEB ACTIVITIES</a></li>
            <li><a href="index.php?menu=user-engagement">USER ENGAGEMENT</a></li>
            <li><a href="index.php?menu=survey">SURVEY</a></li>
            <li><a href="index.php?menu=badges">BADGES</a></li>
            <li><a href="index.php?menu=reporting">REPORTING</a></li>
        </ul>
    </div>
</div>
<script>
/*
$('ul li a').click(function() {
    $('ul li.current').removeClass('current');
    $(this).closest('li').addClass('current');
});*/
</script>
<script>
	var pages = window.location.href;
	var selectedpage = pages.substring(pages.lastIndexOf('/')+1);
	if (selectedpage.length>0){
		$('#nav_dropdown>li>a').removeAttr('id');
		$("#nav_dropdown>li>a[href='" +  selectedpage+ "']").attr('id', 'blue_menu');
	}
</script>