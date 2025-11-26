<?php

namespace Oxygen\Core\API;

use Oxygen\Core\Application;
use Oxygen\Core\Request;
use Oxygen\Core\Response;

/**
 * OxygenAPI - Automatic CRUD API Generator
 * 
 * Automatically generates RESTful CRUD APIs from your database models.
 * Supports filtering, pagination, sorting, and relationships.
 * 
 * @package    Oxygen\Core\API
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // In routes/api.php
 * OxygenAPI::resource('users', User::class);
 * 
 * // This creates:
 * // GET    /api/users       - List all users
 * // GET    /api/users/1     - Get user by ID
 * // POST   /api/users       - Create user
 * // PUT    /api/users/1     - Update user
 * // DELETE /api/users/1     - Delete user
 */
class OxygenAPI
{
    /**
     * Create a RESTful API resource for a model
     * 
     * @param string $name Resource name (e.g., 'users')
     * @param string $modelClass Model class name
     * @param array $options Options (middleware, only, except)
     * @return void
     */
    public static function resource($name, $modelClass, $options = [])
    {
        $router = Application::getInstance()->make(\Bramus\Router\Router::class);
        $basePath = "/api/{$name}";

        // GET /api/users - List all
        if (!isset($options['except']) || !in_array('index', $options['except'])) {
            $router->get($basePath, function () use ($modelClass) {
                static::index($modelClass);
            });
        }

        // GET /api/users/1 - Show one
        if (!isset($options['except']) || !in_array('show', $options['except'])) {
            $router->get("{$basePath}/([0-9]+)", function ($id) use ($modelClass) {
                static::show($modelClass, $id);
            });
        }

        // POST /api/users - Create
        if (!isset($options['except']) || !in_array('store', $options['except'])) {
            $router->post($basePath, function () use ($modelClass) {
                static::store($modelClass);
            });
        }

        // PUT /api/users/1 - Update
        if (!isset($options['except']) || !in_array('update', $options['except'])) {
            $router->put("{$basePath}/([0-9]+)", function ($id) use ($modelClass) {
                static::update($modelClass, $id);
            });
        }

        // DELETE /api/users/1 - Delete
        if (!isset($options['except']) || !in_array('destroy', $options['except'])) {
            $router->delete("{$basePath}/([0-9]+)", function ($id) use ($modelClass) {
                static::destroy($modelClass, $id);
            });
        }
    }

    /**
     * List all records (with pagination and filtering)
     */
    protected static function index($modelClass)
    {
        $request = Request::capture();
        $config = require __DIR__ . '/../../../config/api.php';

        // Get pagination parameters
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(
            $config['pagination']['max_per_page'],
            (int) ($_GET['per_page'] ?? $config['pagination']['per_page'])
        );

        // Get all records
        $allRecords = $modelClass::all();
        $total = count($allRecords);

        // Paginate
        $offset = ($page - 1) * $perPage;
        $records = array_slice($allRecords, $offset, $perPage);

        // Return paginated response
        Response::apiPaginated($records, $total, $page, $perPage)->send();
        exit;
    }

    /**
     * Show a single record
     */
    protected static function show($modelClass, $id)
    {
        $record = $modelClass::find($id);

        if (!$record) {
            Response::apiError('Record not found', 404)->send();
            exit;
        }

        Response::apiSuccess($record)->send();
        exit;
    }

    /**
     * Create a new record
     */
    protected static function store($modelClass)
    {
        $request = Request::capture();
        $data = $request->all();

        try {
            $record = $modelClass::create($data);
            Response::apiSuccess($record, 'Record created successfully', 201)->send();
            exit;
        } catch (\Exception $e) {
            Response::apiError('Failed to create record', 400, ['exception' => $e->getMessage()])->send();
            exit;
        }
    }

    /**
     * Update an existing record
     */
    protected static function update($modelClass, $id)
    {
        $request = Request::capture();
        $data = $request->all();

        try {
            $record = $modelClass::update($id, $data);

            if (!$record) {
                Response::apiError('Record not found', 404)->send();
                exit;
            }

            Response::apiSuccess($record, 'Record updated successfully')->send();
            exit;
        } catch (\Exception $e) {
            Response::apiError('Failed to update record', 400, ['exception' => $e->getMessage()])->send();
            exit;
        }
    }

    /**
     * Delete a record
     */
    protected static function destroy($modelClass, $id)
    {
        try {
            $modelClass::delete($id);
            Response::apiSuccess(null, 'Record deleted successfully')->send();
            exit;
        } catch (\Exception $e) {
            Response::apiError('Failed to delete record', 400, ['exception' => $e->getMessage()])->send();
            exit;
        }
    }

    /**
     * Send JSON response
     */
    protected static function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
