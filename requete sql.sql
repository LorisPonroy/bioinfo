SELECT prot_name,accession FROM ENTRIES NATURAL JOIN PROT_NAME_2_PROT NATURAL JOIN PROTEIN_NAMES NATURAL JOIN COMMENTS WHERE txt_c LIKE '%cardiac%' AND name_kind='recommendedName';

SELECT prot_name,accession FROM entries NATURAL JOIN entries_2_keywords NATURAL JOIN keywords NATURAL JOIN prot_name_2_prot NATURAL JOIN protein_names WHERE kw_label='Long QT syndrome' AND name_kind='recommendedName';

SELECT accession FROM entries NATURAL JOIN proteins where seqLength = (select MAX(seqLength) from proteins)

SELECT accession,COUNT(gene_name_id) AS nb_names FROM entries NATURAL JOIN entry_2_gene_name GROUP BY accession HAVING COUNT(gene_name_id)>2;

SELECT accession,prot_name,name_kind FROM ENTRIES NATURAL JOIN PROT_NAME_2_PROT NATURAL JOIN PROTEIN_NAMES WHERE prot_name LIKE '%channel%';

select accession, prot_name from protein_names NATURAL JOIN prot_name_2_prot NATURAL JOIN (select accession from entries_2_keywords where kw_id IN (select kw_id from keywords where kw_label = 'Long QT syndrome') INTERSECT select accession from entries_2_keywords where kw_id IN (select kw_id from keywords where kw_label = 'Short QT syndrome')) WHERE name_kind = 'recommendedName';

SELECT DISTINCT db_ref FROM entries NATURAL JOIN dbref NATURAL JOIN entries_2_keywords NATURAL JOIN keywords WHERE kw_label = 'Long QT syndrome' HAVING COUNT(accession)>1 GROUP BY db_ref;



