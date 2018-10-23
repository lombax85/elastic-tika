Proof of concept ElasticSearch + Apache Tika for search in files.
Quick startup

mkdir ./web/files

copiare alcuni file in uno di questi formati array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx');
(NOTA è lento iniziare con pochi file)

cd docker-compose
docker-compose up -d

http://localhost

cliccare su scan e attendere
dopo è possibile cliccare su search e visualizzare i risultati