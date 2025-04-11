<?php

function getFeaturedProducts($db, $limit = 6)
{
    $stmt = $db->prepare("SELECT * FROM products WHERE featured = 1 LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsByCategory($db, $category, $limit = 12)
{
    $stmt = $db->prepare("SELECT * FROM products WHERE category = :category LIMIT :limit");
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($db, $id)
{
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Funciones de usuario
function userLogin($db, $email, $password)
{
    $stmt = $db->prepare("SELECT id, password FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user['id'];
    }
    return false;
}

function userRegister($db, $name, $email, $password)
{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $hashed_password);

    return $stmt->execute();
}
