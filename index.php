<?php

use Ousseynou\Bakelicrudphp\URLHelper;
use Ousseynou\Bakelicrudphp\TableHelper ;

   define( 'PER_PAGE', 20 );

   require './vendor/autoload.php';

      $servname = "localhost";
      $dbname = "bakelischool"; 
      $user = "root";
      $pass = "";


   try{
      $pdo = new PDO("mysql:host=$servname; dbname=$dbname", $user, $pass);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if(!$pdo){
         die(mysqli_error($pdo));
      }
      
      /*Sélectionne toutes les valeurs dans la table users*/
      $query = " SELECT * FROM students /*ORDER BY 2*/ ";
      $queryCount = "SELECT COUNT(id) as count FROM students  ";
      $params = [];
      $sortable = ["id", "nom", "prenom", "annee_de_naissance", "note_en_inf", "note_en_gestion_projet" ];
      
      //recherche par nom 
      if(!empty($_GET['q'])){
         $query .= "WHERE nom LIKE :nom ";
         $params ['nom']= "%" . $_GET['q'] . "%";
      }

      //Organisation pour l'affichage par ordre soit croissant ou soit décroissant
      if(!empty($_GET['sort']) && in_array ($_GET['sort'], $sortable)){
         $direction = $_GET['dir'] ?? 'asc';
         if(!in_array($direction, ['asc', 'desc'])){
            $direction = 'asc' ?? 'desc';
         }
         $query .= "ORDER BY " . $_GET[ 'sort' ] . "$direction";      
      }

      //pagination
      $page =(int)($_GET['p'] ?? 1 );
      $offset = ($page-1) * PER_PAGE;
      $query .= " LIMIT " . PER_PAGE ." OFFSET $offset ";


      $sth = $pdo->prepare($query);
      $sth->execute($params);
      /*Retourne un tableau associatif pour chaque entrée de notre table
      *avec le nom des colonnes sélectionnées en clefs*/
      $resultats = $sth->fetchAll(PDO::FETCH_ASSOC);


      //Gérer la pagination de telle sorte qu'il ya ait des bouttons de 'précedente' et 'suivante' 
      $sth = $pdo->prepare($queryCount);
      $sth ->execute();
      $count =(int)$sth->fetch()['count'];

      
      //Pour connaitre le nombre de pages:
      $pages = ceil( $count / PER_PAGE );

      /*print_r permet un affichage lisible des résultats,
      *<pre> rend le tout un peu plus lisible*/
   
      /*echo '<pre>';
      *print_r($resultat);
      *echo '</pre>';
      */
   }
         
   catch(PDOException $e){
      echo "Erreur : " . $e->getMessage();
   }


?>
        

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" >
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <!-- CSS only -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   <!-- CSS only -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
   <div class="container">
      <div class="row">
         <div class="col-md-2">
            <div class="logo">
               <img src="../bakeliexam/img/logo.png" alt="logo">
            </div>
         </div>
         <div class="col-md-8">
            <h1 class="index-title text-center" style="color:green">Liste des Etudiants </h1>
            <h3 class="index-title text-center" style="color:green" >Promotion 2019/2020</h3>
         </div>
         <div class="col-md-2">
            <div class="logo">
               <img src="../bakeliexam/img/volkeno.jfif" alt="logo" style="width: 90px " ; >
            </div>
         </div>
      </div>
   </div>

   <form action="" >
      <div class="form-group">
         <input type="text" class="form-control" name="q" placeholder="Rechercher par Nom" value="<?= htmlentities($_GET['q'] ?? null) ?> " />
      </div>
      <div style="justify-content: center ;align-items: center; text-align: center;">
         <button class="btn btn-success " style="margin:10px;" >Rechercher</button>
      </div>
   </form>

   <table class="table table-striped">
      <thead>
         <tr >
            <th><?= TableHelper::sort('id', "ID", $_GET) ?></th>
            <th><?= TableHelper::sort('nom', 'Nom', $_GET) ?></th>
            <th><?= TableHelper::sort('prenom', 'Prenom', $_GET) ?></th>
            <th><?= TableHelper::sort('annee_de_naissance', 'Annee de naissance', $_GET) ?></th>
            <th><?= TableHelper::sort('note_en_inf', 'Note en inf', $_GET) ?></th>
            <th><?= TableHelper::sort('note_en_gestion_projet', 'Note en gestion de projet', $_GET) ?></th>
            <th colspan="2" class="text-center">Action</th>
         </tr>
      </thead>
      <tbody>

      <?php foreach ($resultats as  $resultat): ?>
         <tr>
            <td>#<?= $resultat['id']?> </td>
            <td><?= $resultat['nom'] ?></td>
            <td><?= $resultat['prenom'] ?></td>
            <td><?= $resultat['annee_de_naissance'] ?></td>
            <td><?= $resultat['note_en_inf'] ?></td>
            <td><?= $resultat['note_en_gestion_projet'] ?></td>
            <td>
               <a href="#/">Edit</a>
            </td>
            <td>
               <a href="#/">Delete</a>
            </td>
         </tr>
      <?php endforeach ?>
      </tbody>
   </table>

   <?php if ($pages > 1 && $page > 1): ?>
      <a href="?<?= URLHelper::withParam($_GET, "p", $page - 1) ?> " class="btn btn-success">Page précédente</a>
   <?php endif ?>
   <?php if ($pages > 1 && $page < $pages): ?>
      <a href="?<?= URLHelper::withParam($_GET, "p", $page + 1) ?> " class="btn btn-success">Page suivante</a>
   <?php endif ?>










   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>