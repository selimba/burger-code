<?php 
	require 'database.php';
	$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description =$price = $category = $image = "";

	if(!empty($_POST)){
		$name = checkInput($_POST['name']);
		$description = checkInput($_POST['description']);
		$category = checkInput($_POST['category']);
		$price = checkInput($_POST['price']);
		$image = checkInput($_FILES['image']['name']);
		$imagePath = '../images/' . basename($image);
		$imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
		$isSuccess = true;
		$isUploadSucces = false;


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
			$priceError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		}
		if(empty($image)){
			$imageError = "Ce champ ne peut pas etre vide";
			$isSuccess = false;
		} else {
			$isUploadSucces = true;
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
					$isUploadSucces = false;
				}
			}
		}
        if($isSuccess && $isUploadSuccess)
        {
            $db = Database::connect();
            $statement = $db->prepare("INSERT INTO items (name,description,price,category,image) values(?, ?, ?, ?, ?)");
            $statement->execute(array($name,$description,$price,$category,$image));
            Database::disconnect();
            header("Location: index.php");
        }


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
			<h1><strong>Ajouter un item</strong></h1>
				<br>
				<form class="form" role="form" action="insert.php" method="post" enctype="multipart/form-data">
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

									echo '<option value="' . $row['id'].   '">' .$row['name']. '</option>';
								}
								Database::disconnect();
							 ?>
						</select>
						<span class="help-inline"><?php echo $priceError;?></span>
					</div>
					<div class="form-group">
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
	</div>	

</body>
</html>