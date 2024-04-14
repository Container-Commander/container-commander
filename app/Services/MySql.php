<?php

namespace App\Services;

class MySql extends BaseService
{
    protected static $category = "Database";

    protected $organization = 'mysql';
    protected $imageName = 'mysql-server';
    protected $defaultPort = 3306;

    public $dockerRunTemplate = '-p "${:port}":3306 \
        -e MYSQL_ROOT_PASSWORD="${:root_password}" \
        -e MYSQL_ALLOW_EMPTY_PASSWORD="${:allow_empty_password}" \
        -e MYSQL_ROOT_HOST="%" \
        -v "${:volume}":/var/lib/mysql \
        "${:organization}"/"${:image_name}":"${:tag}" --default-authentication-plugin=mysql_native_password';

    protected static $displayName = 'MySQL';

    protected function buildParameters(): array
    {
        $parameters = parent::buildParameters();

        $parameters['allow_empty_password'] = $parameters['root_password'] === '' ? '1' : '0';

        return $parameters;
    }
}
