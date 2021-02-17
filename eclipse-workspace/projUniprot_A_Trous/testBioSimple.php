<?php
		function recupererDonnes(){
			// Récupérer dans des variables locales les paramètres du formulaire
			$ac = $_REQUEST['accession'];
			
			//Informations de connexion
			$connexion = oci_connect('c##lponroy_a', 'lponroy_a', 'dbinfo');


			/*Requetes necessaires pour récuperer toutes les informations :*/

			//Requete qui récupères tous les noms d'une entrée
			$reqNames = "SELECT prot_name, name_kind, name_type "
            			. "FROM protein_names NATURAL JOIN prot_name_2_prot "
            			. "where accession = :acces ";
			//Requete qui récupères les informations unique à chaques entrées
			$reqSeq = "select accession, seq, seqLength, seqMass, specie "
				. "from proteins NATURAL JOIN entries "
				. "where accession = :acces ";
			//Requete qui récupères tous les gènes d'une entrée
			$reqGenes = " SELECT gene_name, name_type "
            			. "FROM gene_names NATURAL JOIN entry_2_gene_name "
            			. "where accession = :acces ";
			//Requete qui récupères tous les keywords d'une entrée
			$reqKeywords = "select kw_label "
            			. "from keywords NATURAL JOIN entries_2_keywords "
            			. "where accession = :acces ";
			//Requete qui récupères tous les commentaires d'une entrée
			$reqComments = "select txt_c "
            			. "from comments "
            			. "where accession = :acces ";
			//Requete qui récupères tous les references GO d'une entrée
			$reqGO = "select db_ref "
            			. "from dbref "
            			. "where accession = :acces ";

			$answer = array(); //tableau pour stocker les réponses des requetes.
		//Names
			$ordreNames = oci_parse($connexion, $reqNames);
			oci_bind_by_name($ordreNames, ":acces", $ac);
			oci_execute($ordreNames);
			$names = array();
			$n = 0;
			while (($row = oci_fetch_array($ordreNames, OCI_BOTH)) !=false) {
				$names[$n] = array(
					'name' => $row[0],
					'kind' => $row[1],
					'type' => $row[2]
				);
				$n++;
			}
			oci_free_statement($ordreNames);
			$answer['names'] = $names;
		//Sequence
			$ordreSeq = oci_parse($connexion, $reqSeq);
			oci_bind_by_name($ordreSeq, ":acces", $ac);
			oci_execute($ordreSeq);
			while (($row = oci_fetch_array($ordreSeq, OCI_BOTH)) !=false) {
				$answer['accession'] = $row[0];
				$answer['sequence'] = $row[1];
				$answer['seqLength'] = $row[2];
				$answer['seqMass'] = $row[3];
				$answer['specie'] = $row[4];			
			}
			oci_free_statement($ordreSeq);
		//Genes
			$ordreGenes = oci_parse($connexion, $reqGenes);
			oci_bind_by_name($ordreGenes, ":acces", $ac);
			oci_execute($ordreGenes);
			$genes = array();
			$g = 0;
			while (($row = oci_fetch_array($ordreGenes, OCI_BOTH)) !=false) {
				$genes[$g] = array(
					'name' => $row[0],
					'type' => $row[1]
				);
				$g++;
			}
			oci_free_statement($ordreGenes);
			$answer['genes'] = $genes;
		//Keywords
			$ordreKeywords = oci_parse($connexion, $reqKeywords);
			oci_bind_by_name($ordreKeywords, ":acces", $ac);
			oci_execute($ordreKeywords);
			$keywords = array();
			$k = 0;
			while (($row = oci_fetch_array($ordreKeywords, OCI_BOTH)) !=false) {
				$keywords[$k] = $row[0];
				$k++;
			}
			oci_free_statement($ordreKeywords);
			$answer['keywords'] = $keywords;
		
		//Comments
			$ordreComments = oci_parse($connexion, $reqComments);
			oci_bind_by_name($ordreComments, ":acces", $ac);
			oci_execute($ordreComments);
			$comments = array();
			$c = 0;
			while (($row = oci_fetch_array($ordreComments, OCI_BOTH)) !=false) {
				$comments[$c] = $row[0];
				$c++;
			}
			oci_free_statement($ordreComments);
			$answer['comments'] = $comments;
		//Ref GO
			$ordreGO = oci_parse($connexion, $reqGO);
			oci_bind_by_name($ordreGO, ":acces", $ac);
			oci_execute($ordreGO);
			$GO = array();
			$g = 0;
			while (($row = oci_fetch_array($ordreGO, OCI_BOTH)) !=false) {
				$GO[$g] = $row[0];
				$g++;
			}
			oci_free_statement($ordreGO);
			$answer['go'] = $GO;


			oci_close($connexion);
			return $answer;
		}
            

?>
<html>

    <head> 
	<!-- Framework CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>

    <body>
	<div style="margin:1vw">
		<h5>Résulats de la recherche</h5>
	</div>
	<?php
	$datas = recupererDonnes();
	if(!isset($datas['accession'])){
	?>
		<h1>NO DATAS</h1>
	<?php

	}else{
	?>
	<div class="d-flex justify-content-center">
	<div class="card" style="width: 90vw;">
		<div class="card-header">
			<?=$datas['accession']?>
		</div>
		<div class="card-body">
			
			<p>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNames" aria-expanded="false" aria-controls="collapseNames">
				  	Names
				</button>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSequence" aria-expanded="false" aria-controls="collapseSequence">
				  	Sequence
				</button>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGenes" aria-expanded="false" aria-controls="collapseGenes">
				  	Genes
				</button>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKeywords" aria-expanded="false" aria-controls="collapseKeywords">
				  	Keywords
				</button>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComments" aria-expanded="false" aria-controls="collapseComments">
				  	Comments
				</button>
				<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGO" aria-expanded="false" aria-controls="collapseGO">
				  	Réferences GO
				</button>
			</p>
			<!-- Names -->
			<div class="collapse" id="collapseNames">
  				<div class="card card-body">
					<p class="card-text fs-4">Names :<br></p>
					<p class="lh-1 font-monospace">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Name</th>
								<th scope="col">Kind</th>
								<th scope="col">Type</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($datas['names'] as $name){ ?>
								<tr>
									<td><?=$name['name']?></td>
									<td><?=$name['kind']?></td>
									<td><?=$name['type']?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					</p>
  				</div>
			</div>
			<!-- Sequence -->
			<div class="collapse" id="collapseSequence">
  				<div class="card card-body">
					<p class="card-text fs-4">Sequence :<br><text class="font-monospace fs-6"> <?=$datas['sequence']->load() ?></text></p>
					<p class="card-text fs-4">Length : <?=$datas['seqLength'] ?></p>
					<p class="card-text fs-4">Mass : <?=$datas['seqMass'] ?></p>
  				</div>
			</div>
			<!-- Genes -->
			<div class="collapse" id="collapseGenes">
  				<div class="card card-body">
					<p class="card-text fs-4">Genes :<br></p>
					<p class="lh-1 font-monospace">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Gene Name</th>
								<th scope="col">Gene Type</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($datas['genes'] as $gene){ ?>
								<tr>
									<td><?=$gene['name']?></td>
									<td><?=$gene['type']?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					</p>
  				</div>
			</div>
			<!-- Keywords -->
			<div class="collapse" id="collapseKeywords">
  				<div class="card card-body">
					<p class="card-text fs-4">Keywords :<br></p>
					<p class="lh-1 font-monospace">
					<?php foreach($datas['keywords'] as $keyword){ ?>
						<?=$keyword .'<br>'?>
					<?php } ?>
					</p>
  				</div>
			</div>
			<!-- Comments -->
			<div class="collapse" id="collapseComments">
  				<div class="card card-body">
					<p class="card-text fs-4">Comments :<br></p>
					<p class="lh-1 font-monospace">
					<?php foreach($datas['comments'] as $comment){ ?>
						<?=$comment .'<br>'?>
					<?php } ?>
					</p>
  				</div>
			</div>
			<!-- References GO -->
			<div class="collapse" id="collapseGO">
  				<div class="card card-body">
					<p class="card-text fs-4">References GO :<br></p>
					<p class="lh-1 font-monospace">
					<?php foreach($datas['go'] as $go){ ?>
						<a target="_blank" href="https://www.ebi.ac.uk/QuickGO/term/<?=$go?>">
							<?=$go .'<br>'?>
						</a>
					<?php } ?>
					</p>
  				</div>
			</div>
			
		</div>
	</div>
	</div>
	<?php } //Fin du else?>
    </body>
</html>

