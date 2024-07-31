<!-- 
Author: Omar Abbas Ahmed Osman
Modification date: 31/7/2024
Purpose of the file: This is home page of the website, it shows categories and products, it has a search function
-->
<?php
require "functions.php";
require "connection.php";
session_start();
if (isset($_SESSION['loggedin'])) {
	if ($_SESSION['uid'] == 1000){
	header('Location: admin/index.php');
	exit;
	}
	else {
	header('Location: client/index.php');
	exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="../stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="script/script.js"></script>
</head>
<body>

<!-- NAVBAR -->
	<nav class="navbar navbar-expand navbar-dark bg-dark"> 
	<div class="container-fluid"> 
		<a class="navbar-brand active" href="./">MegabyteStore</a> 
		<ul class="navbar-nav me-auto mb-2 mb-lg-0"> 
		<!-- NAVBAR LINKS -->
		<li class="nav-item"> 
			<a class="nav-link" href="login.php">Login
			<i class="fa fa-sign-in"></i></a>
		</li> 
		<li class="nav-item"> 
			<a class="nav-link" href="register.php">Register
			<i class="fa fa-user-plus"></i></a> 
		</li> 
		<li class="nav-item"> 
			<a class="nav-link" href="reset-password.php">Reset Password
		    <i class="fa fa-cogs"></i></a>
		</li>  
		</li> 
		</ul>
		<!-- SEARCH BOX -->
		<form class="d-flex" action="#" method="POST"> 
		<input class="form-control me-2" type="search" name="searchKey" placeholder="Search All Products"></input>
		<button class="btn btn-primary" type="submit" value="search">Search</button> 
		</form> 
	</div> 
	</nav> 

<!-- show products from database before search -->	
<?php
if (!isset($_POST['searchKey'])){
	// get categories and products from database
$stmt = $conn->prepare('SELECT * FROM categories WHERE 1');
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows == 0) {?>
	<p>No Products!!<p>
	<?php 	
}
else {
	 $categories = mysqli_fetch_all($result, MYSQLI_ASSOC); 
	 foreach ($categories as $category){
	?>
	<div class="container mt-5 mb-3">
	  <div class="card">
		 <div class="card-header">
		 <h4 align="center"><?php echo $category['categoryName']; ?></h4></div>
	  <div class="row">
	  <?php $products = get_products($category['categoryID']);
	  if($products == Null){ ?>				
	  <?php 
      } else {
			foreach ($products as $product){?>
	    <div class="col-sm-3">
		  <div class="card mt-2">
		    <div class="card-header">
			<h4 align="center"><?php echo $product['productName']; ?></h4></div>
			<div class="card-body" align="center">
			<img height="200px" width="200px" alt="Product Image" src="images/<?php echo $product['productImage']; ?>"></img>
			<p class="lead"><?php echo number_format($product['productPrice'], 0); ?> SDG</p>
			<p><font color="red"><?php echo $product['productQty']; ?></font> Available</p>
			<a href="client/product-details.php?productID=<?php echo $product['productID']; ?>" class="btn btn-primary">Details</a>
			</div>
			</div>
		  </div>
	    
		<?php
           }
	   }
		?>
		<div class="col-sm-3">
		  <div class="card border-0" align="center">
			<div class="card-body">
			<h4 align="center">Show The Whole Category</h4><br></br>
			<a href="client/category-details.php?categoryID=<?php echo $category['categoryID']; ?>" class="btn btn-primary"><?php echo $category['categoryName']; ?></a>
			</div>
		  </div>
	    </div>
	  </div>
	</div>
	</div>
	<?php
		}
		}
}
	?>
	<!-- show products from database after search -->	
<?php
if (isset($_POST['searchKey'])){
	
	$searchKey = $_POST['searchKey'];
	$sql = "SELECT productID, productName, productPrice, productQty, productImage FROM products WHERE productQty != 0 AND (productName like '%$searchKey%' or productDesc like '%$searchKey%') ORDER BY productID DESC ";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
      }
      else {
        $products = Null;
      }
	?>
	<div class="container mt-5 mb-3">
	  <div class="row">
	    <div class="col-md-12">
		 <button class="btn btn-primary float mb-3" onclick="goBack();"><i class="fa fa-arrow-left"></i></button>
		  <div class="card">
		    <div class="card-header">
			  <h4>"<?php echo $searchKey; ?>" Search Results</h4>
		    </div>
			<div class="card-body">
			
			    <?php if($products == Null){ ?>
		                <p>No Products!!<p>
						
	            <?php 
				     }else {
				?>
			  <table class="table table-striped" border="0">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Product Image</th>
                  <th>Product Price</th>
                  <th>Product Quantity</th>
				  <th>Product Details</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                <?php foreach ($products as $product) {
				?>				
                  <td><strong><?php echo $product['productName']; ?></strong></td>
                  <td><img class="img-thumbnail" height="100px" width="200px" alt="Product Image" src="images/<?php echo $product['productImage']; ?>"></img></td>
                  <td class="lead"><?php echo number_format($product['productPrice'], 0); ?> SDG</td>
                  <td><font color="red"><?php echo $product['productQty']; ?></font> Available</td>
                  <td>
				    <a href="client/product-details.php?productID=<?php echo $product['productID']; ?>" class="btn btn-primary">Details</a>
				  </td>
                </tr>
				<?php
					   }
					   }
                ?>
              </tbody>
              </table>
	
			</div>
		  </div>
		</div>
	  </div>
	</div>
	<?php
  }
	?>

</body>
</html>