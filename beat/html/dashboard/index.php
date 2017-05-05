<!DOCTYPE html>
<html lang="en">
<head>
<?php include("meta.php"); ?>
</head>
	<body>
    <div id="body-container">
      <div id="body">
            <?php include("header.php"); ?>	
            <div id="content">
                <div class="container">
                    <div class="row">	
                        <div id="theContent">
                                <?php 
                                if($_GET['menu']=='ba-rank'){
                                    include("ba-rank.php");
                                }else if($_GET['menu']=='web-activities'){ 
                                    include("web-activities.php");
                                }else if($_GET['menu']=='user-engagement'){ 
                                    include("user-engagement.php");
                                }else if($_GET['menu']=='survey'){ 
                                    include("survey.php");
                                }else if($_GET['menu']=='reporting'){ 
                                    include("reporting.php");
                                }else if($_GET['menu']=='badges'){ 
                                    include("badges.php");
                                }else if($_GET['menu']=='top10-venue'){ 
                                    include("top10-venue.php");
                                }else if($_GET['menu']=='top10-search'){ 
                                    include("top10-content-search.php");
                                }else{ 
                                    include("home.php");
                                }?>
                            
                        </div> <!-- /#theContent -->
                    </div> <!-- /.container -->
                </div> <!-- /.row -->
            </div> <!-- /#theContent -->
            <?php include("footer.php"); ?>	
      </div> <!-- /#body -->
    </div> <!-- /#body-container -->
  </body>
</html>
