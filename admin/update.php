<?php 
	require 'database.php';

	if(!empty($_GET['id'])){
		$id = checkInput($_GET['id']);
	}
	$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description =$price = $category = $image = "";

	if(!empty($_POST))
	{
		$name = 			checkInput($_POST['name']);
		$description = 		checkInput($_POST['description']);
		$category = 		checkInput($_POST['category']);
		$price = 			checkInput($_POST['price']);
		$image = 			checkInput($_FILES['image']['name']);
		$imagePath = 		'../images/' . basename($image);
		$imageExtension = 	pathinfo($imagePath, PATHINFO_EXTENSION);
		$isSuccess =		true;


		if(empty($name)){
			$nameError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		}
		if(empty($description)){
			$descriptionError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		}
		if(empty($category)){
			$categoryError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		}
		if(empty($price)){
			$isImageUpdated = false;
		}
		if(empty($image)){
			$imageError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		} else {
			$isUploadSucces = true;
			$isImageUpdated = true;
			if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif"){
				$imageError = "les fichier autorise sont jpg,png,jpeg,gif";
				$isUploadSucces = false;
			}
			if(file_exists($imagePath)){
				$imageError = "le fichier existe deja";
				$isUploadSucces = false;
			}
			if($_FILES["image"]["size"] > 5000){
				$imageError = "Le fichier ne doit pas depasser 500KB";
				$isUploadSucces = false;
			} 
			if($isUploadSucces){
				if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)){
					$imageError = "il y a eu des problemes dans l'upload";

				}
			}
		}
        if(($isSuccess && $isImageUpdated && $isUploadSucces) || ($isSuccess && !$isImageUpdated))
        {
            $db = Database::connect();
            if($isImageUpdated){
            	$statement = $db->prepare("UPDATE items = ?, description = ?, category=?, price = ?, image = ? WHERE id = ?");
            	$statement->execute(array($name,$description,$price,$category,$image,$id));
            } else {
            	$statement = $db->prepare("UPDATE items = ?, description = ?, category=?, price = ? WHERE id = ?");
            	$statement->execute(array($name,$description,$price,$category,$id));
            }
            
            Database::disconnect();
            header("Location: index.php");
        }
        else if($isImageUpdated && !$isUploadSucces){
        $db = Database::connect();
        $statement = $db->prepare("SELECT * FROM items WHERE id = ?");
        $statement->execute(array($id));
        $item = $statement->fetch();
        $image = $item['image'];
        Database::disconnect();
        }

	}
	else{
		$db = Database::connect();
        $statement = $db->prepare("SELECT * FROM items WHERE id = ?");
        $statement->execute(array($id));
        $item = 			$statement->fetch();
        $name = 			$item['name'];
        $description = 		$item['description'];
        $category = 		$item['category'];
        $price = 			$item['price'];
        $image = 			$item['image'];
        Database::disconnect();


	}

	function checkInput($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
	}
 ?>


<!DOCTYPE html>
<html>
<head>
	<title>Burger Restaurant</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Holtwood+One+SC" rel="stylesheet">
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<h1 class="text-logo">
		<span class="glyphicon glyphicon-cutlery"></span> Restaurant Burger <span class="glyphicon glyphicon-cutlery"></span>
	</h1>	
	<div class="container admin">
		<div class="row">
			<div class="col col-md-6"> 
				<h1><strong>Modifier un item</strong></h1>
					<br>
					<form class="form" role="form" action="<?php echo 'update.php?id=' .$id; ?>" method="post" enctype="multipart/form-data">
						<div class="form-group">
							<label for="name">Nom:</label>
							<input type="text" class="form-control" id="name" placeholder="Nom" name="name"
							value="<?php echo $name;?>">
							<span class="help-inline"><?php echo $nameError;?></span>
						</div>
						<div class="form-group">
							<label for="description">Description:</label>
							<input type="text" class="form-control" id="description" placeholder="Description" name="description" value="<?php echo $description;?>">
							<span class="help-inline"><?php echo $nameError;?></span>
						</div>
						<div class="form-group">
							<label for="price">Prix:</label>
							<input type="number" step="0.01" class="form-control" id="price" placeholder="Prix" name="price"
							value="<?php echo $price;?>">
							<span class="help-inline"><?php echo $descriptionError;?></span>
						</div>
						<div class="form-group">
							<label for="category">Categorie:</label>
							<select class="form-control" id="category" name="category">
								<?php 
									$db = Database::connect();
									foreach($db->query('SELECT * FROM categories') as $row){
										if($row['id'] == $category){
											echo '<option selected="selected" value="' . $row['id'].   '">' .$row['name']. '</option>';
										} else{
											echo '<option value="' . $row['id'].   '">' .$row['name']. '</option>';
										}
										
									}
									Database::disconnect();
								 ?>
							</select>
							<span class="help-inline"><?php echo $priceError;?></span>
						</div>
						<div class="form-group">
							<label>Image:</label>
							<p> <?php echo $image; ?> </p>
							<label for="image">Selectionner une Image:</label>
							<input type="file" id="image" name="image">
							<span class="help-inline"><?php echo $imageError;?></span>
						</div>
					<br>
					<div class="form-actions">
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"> Ajouter</span></button>
						<a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"> Retour</span></a>
					</div>
					</form>
			</div>
			<div class="col col-md-6">
				<div class="thumbnail">
					<img src="<?php echo '../images/'. $image ; ?>" alt="...">
					<div class="price"><?php echo number_format((float)$price,2,'.',''); ?> €</div>
					<div class="caption">
						<h4><?php echo $name; ?></h4>
						<p><?php echo $description; ?></p>
						<a href="#" class="btn btn-order" role="button">
							<span class="glyphicon glyphicon-shopping-cart"></span>
										Commander
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>	

</body>
</html>