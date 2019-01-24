<?php
ob_start();
session_start();
error_reporting(0);
include "data/conn.php";
include "data/constants.php";
include "data/sqlinjection.php";
include "data/youtubeimagegrabber.php";
include "data/groups.php";
include "data/feedbacks.php";
include "data/listings.php";
include "data/listingfiles.php";
include "data/galleries.php";
include "data/videos.php";
include "data/extendtrip.php";
include "data/adds.php";
include "data/testimonials.php";
include "data/menu.php";
include "data/metahome.php";
include "data/listingdate.php";
include("data/linkexchange.php");
include("data/blog.php");
include ("data/comment.php");
include ("data/cities.php");

$conn = new Dbconn();
$groups	= new Groups();
$feedbacks = new Feedbacks();
$listings = new Listings();
$listingFiles = new ListingFiles();
$galleries = new Galleries();
$videos = new Videos();
$metahome = new metaHome();
$datelisting	= new Datelisting();
$extendTrip = new extendTrip;
$exchange  = new Exchange();
$adds = new Adds();
$testimonials = new Testimonials();
$menu = new menu();
$blog  = new Blog;
$comment = new Comment;
$cities = new Cities;
//print_r($_GET);

if(empty($_SERVER['QUERY_STRING']))
$url = true;
else
$url = false;

if(isset($_GET['title'])){
	$title = $_GET['title'];
	$url = true;
if(file_exists("includes/".$title.".php")){
	$_GET['action'] = $title;
	$url = true;
}
else
{
	$row = $listings -> getByURLName($title);
	if($row)
	{
		$listId = $row['id'];
		$_GET['listId'] = $row['id'];
		$url = true;
	}
	else
	{
		$row = $groups -> getByURLName($title);
		if($row)
		{
			if(isset($_GET['action'])){
				$_GET['id'] = $row['id'];
				$url = true;
			}
			else
			{
				$linkId = $row['id'];
				$_GET['linkId'] = $row['id'];	
				$url = true;	
			}
		}
	}
}
}

if(!$url && !isset($_GET['cate_id']) && !isset($_GET['blogId']) && ($_GET['action']!='blog')){
	header("Location:http://".$_SERVER['HTTP_HOST']);
	exit;	
}


$linkId = cleanQuery($_GET['linkId']);
$listId = cleanQuery($_GET['listId']);
$galleryId  = cleanQuery($_GET['galleryId']);

if(isset($_GET['linkId']))
{
	$title = $groups -> getPageTitle($linkId);
	$keyword = $groups -> getPageKeyword($linkId);
	$description = $groups -> getMetaDescription($linkId);
}
elseif(isset($_GET['listId']))
{
	
	$title = $listings -> getPageTitle($listId);
	if(empty($title))
		$title = $listings->getWhatById("title",$listId); 
	
	$keyword = $listings -> getPageKeyword($listId);
	
	$description = $listings -> getMetaDescription($listId);	
	
	
} else if($_GET['action']=='blog' && !isset($_GET['cate_id'])){
	
	$title = $groups -> getPageTitle(173);
	$keyword = $groups -> getPageKeyword(173);
	$description = $groups -> getMetaDescription(173);
		
} else if($_GET['blogId']){
	$row = $blog->getById($_GET['blogId']);
	$title = $row['title'];
	if(!empty($row['pageTitle']))
	$title = $row['pageTitle'];
	$keyword = $row['metaKeyword'];
	$description = $row['metaDescription'];
	
} else if($_GET['cate_id']){
	$row = $cities->getById($_GET['cate_id']);
	$title = "Category .:. ".$row['title'];	
	$keyword = $description = $row['title'];
}
elseif(isset($_GET['listingId']))
{
	$row = $galleries -> getParentDetailsById($galleryId);
	$title = $row['title'];
	$keyword = $row['keyword'];
	$description = $row['metaDescription'];
}else if(isset($_GET['title'])){
	$title = true;
	$title = $groups -> getPageTitle($groups->getWhatByUrlName("id",$_GET["title"]));
	if(empty($title)){
		$title = $groups->getWhatById("name",$groups->getWhatByUrlName("id",$_GET["title"]));
		if(empty($title)){
			$title = false;
		}	
	}
	$keyword = $groups -> getPageKeyword($groups->getWhatByUrlName("id",$_GET["title"]));
	$description = $groups -> getMetaDescription($groups->getWhatByUrlName("id",$_GET["title"]));
	
}else if(isset($_GET['action'])){
	$title = true;
	$title = $groups -> getPageTitle($groups->getWhatByUrlName("id",$_GET["title"]));
	if(empty($title)){
		$title = $groups->getWhatById("name",$groups->getWhatByUrlName("id",$_GET["title"]));
		if(empty($title)){
			$title = false;
		}	
	}
	$keyword = $groups -> getPageKeyword($groups->getWhatByUrlName("id",$_GET["title"]));
	$description = $groups -> getMetaDescription($groups->getWhatByUrlName("id",$_GET["title"]));
	
}else{
	
	$res = $metahome -> getById(1);
	$row = $conn -> fetchArray($res);
	$title = $row['pageTitle'] ;
	$keyword = $row['pageKeyword'];
	$description = $row['metaDescription'];
	
	
}

if(isset($_GET["action"]) && !isset($_GET["page"])){
	if($title)
	$action = " - ".strtoupper(strtolower($_GET["action"]));
	else 
	$action =  strtoupper(strtolower($_GET["action"]));	
}
else if(isset($_GET["page"]) && isset($_GET["action"]) && !isset($_GET["title"])){

	$action = $_GET["action"]." - Page ".strtoupper(strtolower($_GET["page"]));		
}
if(isset($_GET["linkId"]) && isset($_GET["page"])){
	$action = " - Page ".strtoupper(strtolower($_GET["page"]));	
}

function altTag($field,$sql_array,$image_name){
	if(!empty($sql_array[$field]) && !empty($field)){
		$alt = $sql_array[$field];	
	} else{
		$image_name = str_replace("-"," ",current(explode(".",$image_name)));
		$image_name = str_replace("_"," ",current(explode(".",$image_name)));
		$image_name = preg_replace('/[0-9]+/', '', current(explode(".",$image_name)));
		$alt= $image_name;
	}
	
	return $alt;
}
	
	
	function getLink($resources){
			if($resources['linkType']=="Link"){
				$link=$resources['contents'];	
			}
			else {
				$link=$resources['urlname'].".html";	
			}
			return $link;	
	}
	
	function findOrderlize($interger){
		
	 switch($interger){
						case 1:
						$orderlize = "st";
						break;
						case 2:
						$orderlize = "nd";
						break;
						case 3:
						$orderlize = "rd";
						break;
						default:
						$orderlize = "th";
						break;   
				   }
				   
				   return $orderlize;	
	}
function ratingSet($statement){
		switch($statement){	
			case "Poor":
			$rating = 1;
			break;
			case "Fair":
			$rating = 2;
			break;
			case "Good":
			$rating = 3;
			break;
			case  "Very Good":
			$rating = 4;
			break;
			case "Excellent";
			$rating = 5;
			break;	
		}
		return $rating;
	}
	

include("includes/feedbackprocess.php");
include("includes/testimonialprocess.php");
include("data/mis.func.php");
include("formaturl.php");
include("includes/quickqueryprocess.php");


if(isset($_GET['linkId'])){
	
	$id = $_GET['linkId'];
	$result = $groups->getById($id);
	$rq = $conn->fetchArray($result);
	if($rq['linkType']=='Trips Page')
		$p = false;
	 else		
		$p = true;	
	
} else {
	
	$p = true;
}


if(isset($_GET['blogId']) || $_GET['action']=='blog'){
$p = false;
}

function clearfix($lg,$sm){
	global $x;
	if($x%$lg==0)
	echo "<div class=\"clearfix visible-lg-block\"></div>";	
	if($x%$lg==0)
	echo "<div class=\"clearfix visible-md-block\"></div>";	
	if($x%$sm==0)
	echo "<div class=\"clearfix visible-sm-block\"></div>";	
}

require_once('recaptchalib.php');
$secret = "6LeyGxwTAAAAAC1pPezHCE14d-wswZTDsGeaeKaO";	
$siteKey = "6LeyGxwTAAAAAP4Q6Iz6YUYcIZSQM6pMQogF45J3"; // you got this from the signup page

				

if(isset($_POST['btnSubmit']))
{
										
$reCaptcha = new ReCaptcha($secret);
// Was there a reCAPTCHA response?
if ($_POST["g-recaptcha-response"]) {
$resp = $reCaptcha->verifyResponse(
$_SERVER["REMOTE_ADDR"],
$_POST["g-recaptcha-response"]
);
}

$success="";


$name = $_POST['full_name'];										
$email = $_POST['email_address'];
$message = $_POST['textarea_sm'];


if(empty($name) || empty($email) ){
$error = "Email or Name can't be blank.";
}
//else if($_SESSION['valid'] != $_POST['cap_code']){
else if(!($resp != null && $resp->success)){

$error = "Please proceed the security.";
}else{	


$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html; charset=utf-8" . "\r\n";
// More headers
$headers .= 'From: ' . $name . ' <'.SITE_EMAIL.'>' . "\r\n";
//$headers .= 'Cc:'.SITE_EMAIL.'' . "\r\n";

$subject = "Quick inquiry on ".WEBSITE;

$msg="<strong>Name:</strong> $name <br />
<strong>Email:</strong> : $email <br />

<strong>Message:</strong> : $message <br />
";

//echo "hi";
//echo SITE_EMAIL;
mail(SITE_EMAIL,$subject,$msg,$headers);	


$success = "Thanks for Your Message. We will Contact you Shortly.";
$name = $address = $email = $message = "";
}




}
 ?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
         <meta name="viewport" content="width=device-width, initial-scale=1">
       <meta name="keywords" content="<?php echo $keyword.$action; ?>" />
        <meta name="description" content="<?php echo $description.$action; ?>" />
        <title><?php if(!empty($title)) echo $title.$action; else if(isset($_GET["action"])) echo $action; else if(isset($_GET["page"])) echo $action; else echo $groups->getNameByTitle($_GET['linkId']); ?></title>
        <?php include("baselocation.php"); ?>
        <link rel="canonical" href="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']; ?>" />
       
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/slippry.css">
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/main.css">
        <link href="css/animate.css" rel="stylesheet">

        <script src="js/vendor/modernizr.min.js"></script>
    </head>
    <body>
    	<div class="container">
        	<header id="header">
            	<div class="row clearfix">
                	<div class="col-md-5">
                    	<div class="logo-sm">
                        	<a href=""><img src="img/logo.jpg" class="img-responsive" alt="Sero Lab"></a>
                        </div>
                    </div>
                    
                    <div class="col-md-5 col-md-offset-2">
                    	<div class="right-part clearfix">
                        	<div class="email-phone pull-right">
                            	<ul class="list-inline clearfix">
                                	<li><i class="fa fa-phone-square" aria-hidden="true"></i> 977-1-5546246 </li>
                                    <li><i class="fa fa-envelope-o" aria-hidden="true"></i> serolab@wlink.com.np</li>
                                </ul>
                            </div>
                            <div class="clearfix"></div>
                            
                            <div class="top-menu clearfix">
                            	<ul class="list-inline text-right">
                                <?php $result = $groups->getByTypeParentId("Top Links",0);
									while($r = $conn->fetchArray($result)): ?>
                                	<li><a href="<?php echo getLink($r); ?>"><?php echo $r["name"]; ?></a></li>
                                    <?php endwhile; ?>
                                </ul>
                            </div><!-- top menu div end here -->
                        
                        </div><!-- right part div end here -->
                    </div>                
                </div>            
            </header><!-- header div end here -->
   		</div>
        
        <div class="menu">
        	<div class="container">
        	<nav role="navigation"  id="nav">
	      	
                     
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                        
                          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                          </button>
                          
                        </div>
                    
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                          <ul class="nav navbar-nav">
		   
			<li class="active"><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>"><span class="glyphicon glyphicon-home"></span> <span class="sr-only">(current)</span></a></li>
			 <?php $main_navigation = $groups->getByTypeParentId("Header Links",0);
	$l=0;
	while($r = $conn->fetchArray($main_navigation)) { ?>
      <li <?php if($r['linkType']=='Normal Group') echo 'class="dropdown"'; ?>><a href="<?php if($r['linkType']=='Normal Group')echo 'javascript:void(0)'; else echo getLink($r); ?>" <?php if($r['linkType']=='Normal Group') { ?> class="dropdown-toggle" data-toggle="dropdown" <?php  } ?>><?php echo $r['name']; ?> <?php if($r['linkType']=='Normal Group'){ ?>  <i class="fa fa-angle-down"></i> <?php } ?></a> 
      
      <?Php $menu->dropDown($r,$l); ?>
      </li>
      
      <?php } ?>
      
		  </ul>
                        </div><!-- navbar-collapse -->

</nav>
</div>
</div><!-- menu div end here -->


<?php if(count($_GET)==0){ ?>
<section class="slideshow clearfix">

	

	<ul id="demo1">
    	<?php $result = $galleries->getByGroupId(16);
			while($r = $conn->fetchArray($result)): ?>
				<li><a href="#slide1"><img src="<?php echo CMS_IMAGES_DIR.$r["ext"]; ?>" alt="<?php echo $r["caption"]; ?>"></a></li>
			<?php endwhile; ?>
			</ul>
            
</section><!-- section div end here -->
<?php } ?>


<section class="container">

	<?php 
				if($_GET['action']){
						include("includes/".$_GET['action'].".php");
					} else if(isset($_GET['linkId'])){
						include("includes/cmspage.php");
					}else if(isset($_GET['bookId'])){
						include("includes/booking.php");
					}elseif(isset($_GET['listId'])){
						include "includes/showlistsingle.php";
					}elseif(isset($_GET['galleryId'])){
						include("includes/showgallerysingle.php");
					}elseif($_GET['grab']){
						include("includes/cmspage.php");
					}
					elseif($_GET['blogId']){
						include("includes/blogdetails.php");
					}
					else {
?>

	<article id="article">
    <?php $result = $groups->getById(17);
		while($r = $conn->fetchArray($result)): ?>
    	<h1 class="text-center"><?php echo $r["name"]; ?></h1>
        <p class="text-center"><?php echo strip_tags($r["shortcontents"]); ?>

</p>
<a href="<?php echo $r["urlname"]; ?>.html" class="btn btn-default">Read More</a>
<?php endwhile; ?>
    </article>
    
    
    <div class="partner-div">
    	<h3 class="text-center">Partnership for Better <strong>Healthcare</strong></h3>
        <div class="row">
        	<div class="col-md-3 col-md-offset-3">
            	<a href="" class="btn btn-green">Pristine Techno Centre</a>
            </div>
            <div class="col-md-3">
            		<a href="" class="btn btn-green">Precious Nano Centre</a>
            </div>
        </div>  
    </div><!-- partner div end here -->   
    <?php } ?>    
</section><!-- section div end here -->


<?php if(empty($_SERVER['QUERY_STRING'])){ ?>
<div class="container-fluid product">
	<div class="container">
    <h2 class="text-center">Products</h2>
    
    	<div class="row">
        <?php $result = $listings->getMainListingsWithLimit(8);
			while($r = $conn->fetchArray($result)): ?>
        	<div class="col-md-3 col-sm-6">
            	<div class="product-list">
                	<a href="<?php echo $r["urltitle"]; ?>.html"><img src="<?php echo CMS_LISTINGS_DIR.$r["id"].".".$r["ext"]; ?>" class="img-responsive" alt="<?php echo $r["title"]; ?>"></a>
                    <h3><a href="<?php echo $r["urltitle"]; ?>.html"><?php echo $r["title"]; ?> [+]</a></h3>
                </div>
            </div>
            <?php endwhile; ?>
            
             
            
            
        </div>
    </div>
</div><!-- container fluid div end here -->


<section class="download-section">
<div class="container">
	<div class="row">
    	<div class="col-md-6 col-sm-6">
        	<div class="download-brochure">
            	<div class="icon-folder"><img src="img/paf.png" alt="Download PDF file"></div>
                <div class="folder-info">
                <h3><a href="files/download/<?php echo $groups->getWhatById("contents",34); ?>"><?php $title = $groups->getWhatById("name",35); $title_explode = explode(" ",$title); foreach($title_explode as $k=>$v){ echo $title_explode[$k]; echo ($k==0) ? " " : " "; } ?></a></h3>
                
</div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
        	<div class="download-brochure">
            	<div class="icon-folder"><img src="img/certificate.png" alt="Download PDF file"></div>
                <div class="folder-info">
                <h3><a href="files/download/<?php echo $groups->getWhatById("contents",35); ?>"><?php $title = $groups->getWhatById("name",35); $title_explode = explode(" ",$title); foreach($title_explode as $k=>$v){ echo $title_explode[$k]; echo ($k==0) ? " " : " "; } ?></a></h3>
                
</div>
            </div>
        </div>
    </div>
    </div>
</section><!-- download section div end here -->


<section class="line-section" style="display:none;">
	<div class="container">
	<div class="row clearfix">
    	<div class="col-md-6 col-sm-6">
        	<div class="line-list">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="line-detai">
                        	<h3><a href="">INSTRUMENTATION LINE</a></h3>
                            <p>Years of research and development to offer cutting-edge equipment, Made in Italy quality and the highest safety standards.</p>
                            <a href="" class="btn btn-default">More Info</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<a href=""><img src="img/product-list.jpg" class="img-responsive" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6">
        	<div class="line-list clearfix">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="line-detai">
                        	<h3><a href="">INSTRUMENTATION LINE</a></h3>
                            <p>Years of research and development to offer cutting-edge equipment, Made in Italy quality and the highest safety standards.</p>
                            <a href="" class="btn btn-default">More Info</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<a href=""><img src="img/product-list.jpg" class="img-responsive" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    
    <div class="info-social">
    	<p>Sero Lab suppliers for high quality Medical Equipment, Hospital Supplies and Surgical Instruments. Sero Lab, Nepal is a leading manufacturer and suppliers of Hospital Furniture, Orthopedic Implants, Medical Disposables and other hospital medical supplies. Sero Lab is Nepal based company.</p> 

    	
    </div>
    
    </div>
</section><!-- line section div end here -->
<?php } ?>

<section class="extra-links">
	<div class="container">
    	<div class="row">
        	<div class="col-md-3 col-sm-6">
            	<div class="short-info-txt">
                <h3><img src="img/sm-logo.jpg" alt="Sero Lab"></h3>
                	<p>
				<?php echo $groups->getWhatById("shortcontents",40); ?>

                    </p>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
            	<div class="short-info-txt">
                <h3>Quality Policy</h3>
                	<p>
                    	<?php echo $groups->getWhatById("shortcontents",41); ?>
                    </p>
                </div>
            </div>
            
            
            <div class="col-md-3 col-sm-6">
            	<div class="ex-list-panel">
                <h3>Others</h3>
                	<ul>
                    <?php $result = $groups->getByParentId(19);
							while($r = $conn->fetchArray($result)): ?>
                    			<li><a href="<?php echo getLink($r) ?>"><?php echo $r["name"]; ?></a></li>
                        	<?php endwhile; ?>
                        
                    </ul>
                </div>
            </div>
            
            
            <div class="col-md-3 col-sm-6">
            	<div class="ex-list-panel">
                <h3>Connect With Us</h3>
                	<ul>
                    	<li><a href="<?php echo $groups->getWhatById("contents",28); ?>" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",28); ?></a></li>
                        <li><a href="<?php echo $groups->getWhatById("contents",29); ?>"><i class="fa fa-twitter-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",29); ?></a></li>
                        <li><a href="<?php echo $groups->getWhatById("contents",30); ?>"><i class="fa fa-google-plus-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",30); ?></a></li>
                        <li><a href="<?php echo $groups->getWhatById("contents",31); ?>"><i class="fa fa-rss-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",31); ?></a></li>
                        <li><a href="<?php echo $groups->getWhatById("contents",32); ?>"><i class="fa fa-youtube-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",32); ?></a></li>
                        <li><a href="<?php echo $groups->getWhatById("contents",33); ?>"><i class="fa fa-linkedin-square" aria-hidden="true"></i> <?php echo $groups->getWhatById("name",33); ?></a></li>
                                               
                    </ul>
                </div>
            </div>
            
            
        </div>
    </div>
</section><!-- section links div end here -->



<footer id="footer">
	<div class="container">
    	<div class="row clearfix">
        	<div class="col-md-9 col-sm-9">
            	<div class="footer-link">
                	<ul class="list-inline">
                    <?php $result = $groups->getByParentId(36);
						while($r = $conn->fetchArray($result)){ ?>
                    	<li><a href="<?php echo getLink($r); ?>"><?php echo $r["name"]; ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <p class="cpright">
                	Copyright © <?php echo date("Y"); ?> Sero Lab Nepal. All Rights Reserved. Designated trademarks and brands are the property of their respective owners. <br />
Use of this Web site constitutes acceptance of the Sero Lab User Agreement.
                </p>
            </div>
            
            
            <div class="col-md-3 col-sm-3">
            <p class="text-right powered">Powered By: <a href="http://www.weblinknepal.com/" target="_blank">Weblink Nepal</a></p>
            
            </div>
            
        </div>
    </div>
</footer>


<script src="js/jquery-2.2.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/slippry.min.js"></script> 
<script>
	$(function() {
		var demo1 = $("#demo1").slippry();
	});
</script>
        
</body>
</html>
