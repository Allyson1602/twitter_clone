<?php

    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model{
        private $id, $id_usuario, $tweet, $data;

        public function __get($atributo){
            return $this->$atributo;
        }
        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        // cadastrar
        public function cadastrar(){
            $query = "INSERT INTO tweets(id_usuario, tweet) VALUES(:id_usuario, :tweet)";
            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));

            $stmt->execute();

            return $this;
        }

        // recuperar
        public function getAll(){
            $query = "SELECT 
                t.id, 
                t.id_usuario, 
                u.nome, 
                t.tweet, 
                DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') AS data
            FROM 
                tweets AS t
                LEFT JOIN usuarios AS u ON (t.id_usuario = u.id)
            WHERE 
                t.id_usuario = :id_usuario
                OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
            ORDER BY
                t.data DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // remover tweet
        public function deletarTweet(){
            $query = "DELETE FROM tweets WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $this;
        }

        public function getTotalTweets(){
            $query = "SELECT count(*) AS total_tweet FROM tweets WHERE id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }

?>