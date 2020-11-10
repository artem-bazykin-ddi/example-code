<?php
declare(strict_types=1);

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @var Client|null
     */
    private $client;

    /**
     * UserImport constructor.
     *
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * @param array $row
     *
     * @return User
     */
    public function model(array $row): User
    {
        return new User([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'job_role' => $row['job_role'],
            'job_dept' => $row['job_dept'],
            'permission' => User::APP_USER,
            'client_id' => $this->client ? $this->client->id : null
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email',
        ];
    }

    /**
     * @return array
     */
    public function customValidationAttributes(): array
    {
        return ['2' => 'email'];
    }
}
