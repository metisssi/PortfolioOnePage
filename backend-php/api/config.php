<?php
// ============================================================
//  config.php — MongoDB connection via ext-mongodb (no Composer!)
//  EDIT the values below after uploading to your hosting
// ============================================================

// --- MONGODB ---
// For MongoDB Atlas:
// define('MONGO_URI', 'mongodb+srv://username:password@cluster.xxxxx.mongodb.net/?retryWrites=true&w=majority');
// For local MongoDB:
define('MONGO_URI', 'mongodb+srv://prtfolio:A2007r2013@cluster0.yxtmtcv.mongodb.net/?prtfolio=Cluster0');
define('MONGO_DB', 'bolesti_zad');

// --- AUTH ---
define('ADMIN_PASSWORD', '123456');
define('JWT_SECRET', 'your_jwt_secret_key_change_me_123!');

// --- CORS ---
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- MongoDB Connection (raw driver) ---
function getManager(): MongoDB\Driver\Manager {
    static $manager = null;
    if ($manager === null) {
        $manager = new MongoDB\Driver\Manager(MONGO_URI);
    }
    return $manager;
}

function getNamespace(string $collection): string {
    return MONGO_DB . '.' . $collection;
}

/**
 * Find documents in a collection
 */
function mongoFind(string $collection, array $filter = [], array $options = []): array {
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = getManager()->executeQuery(getNamespace($collection), $query);
    
    $results = [];
    foreach ($cursor as $doc) {
        $results[] = bsonToArray($doc);
    }
    return $results;
}

/**
 * Find one document
 */
function mongoFindOne(string $collection, array $filter = []): ?array {
    $results = mongoFind($collection, $filter, ['limit' => 1]);
    return $results[0] ?? null;
}

/**
 * Insert one document, returns inserted _id as string
 */
function mongoInsertOne(string $collection, array $document): string {
    $bulk = new MongoDB\Driver\BulkWrite();
    $id = $bulk->insert($document);
    getManager()->executeBulkWrite(getNamespace($collection), $bulk);
    return (string) $id;
}

/**
 * Update documents matching filter
 */
function mongoUpdateOne(string $collection, array $filter, array $update, bool $upsert = false): int {
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update($filter, $update, ['upsert' => $upsert]);
    $result = getManager()->executeBulkWrite(getNamespace($collection), $bulk);
    return $result->getModifiedCount() + $result->getUpsertedCount();
}

/**
 * Delete documents matching filter
 */
function mongoDeleteOne(string $collection, array $filter): int {
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter, ['limit' => 1]);
    $result = getManager()->executeBulkWrite(getNamespace($collection), $bulk);
    return $result->getDeletedCount();
}

/**
 * Count documents
 */
function mongoCount(string $collection, array $filter = []): int {
    $command = new MongoDB\Driver\Command([
        'count' => $collection,
        'query' => (object) $filter
    ]);
    $cursor = getManager()->executeCommand(MONGO_DB, $command);
    return $cursor->toArray()[0]->n ?? 0;
}

/**
 * Convert BSON object to plain PHP array with string _id
 */
function bsonToArray($doc): array {
    $arr = (array) $doc;
    $result = [];
    
    foreach ($arr as $key => $value) {
        if ($value instanceof MongoDB\BSON\ObjectId) {
            $result[$key] = (string) $value;
        } elseif ($value instanceof MongoDB\BSON\UTCDateTime) {
            $result[$key] = $value->toDateTime()->format('c');
        } elseif (is_object($value)) {
            $result[$key] = bsonToArray($value);
        } elseif (is_array($value)) {
            $result[$key] = array_map(function ($v) {
                return is_object($v) ? bsonToArray($v) : $v;
            }, $value);
        } else {
            $result[$key] = $value;
        }
    }
    return $result;
}

/**
 * Create ObjectId from string
 */
function toObjectId(string $id): MongoDB\BSON\ObjectId {
    return new MongoDB\BSON\ObjectId($id);
}

// --- Helpers ---
function jsonResponse($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getRequestBody(): array {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

function getMethod(): string {
    return $_SERVER['REQUEST_METHOD'];
}

// --- Simple JWT (HS256) ---
function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_encode(array $payload): string {
    $header = base64url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['iat'] = time();
    $payload['exp'] = time() + (7 * 24 * 3600);
    $payloadEncoded = base64url_encode(json_encode($payload));
    $signature = base64url_encode(hash_hmac('sha256', "$header.$payloadEncoded", JWT_SECRET, true));
    return "$header.$payloadEncoded.$signature";
}

function jwt_decode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $signature] = $parts;
    $validSignature = base64url_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));

    if (!hash_equals($validSignature, $signature)) return null;

    $data = json_decode(base64url_decode($payload), true);
    if (!$data || !isset($data['exp']) || $data['exp'] < time()) return null;

    return $data;
}

function requireAuth(): array {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
        $decoded = jwt_decode($matches[1]);
        if ($decoded) return $decoded;
    }
    jsonResponse(['message' => 'Neplatný token'], 403);
    return [];
}