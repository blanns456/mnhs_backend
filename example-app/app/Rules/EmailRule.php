<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmailRule implements ValidationRule
{
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = Auth::id();

        // If updating, check if the user owns the record
        if ($this->id) {
            $ownsRecord = DB::table('student_personal_information')
                ->where('id', $this->id)
                ->exists();

            if ($ownsRecord) {
                // User owns the record, allow them to proceed
                return;
            }
        }

        // Check uniqueness in students table
        $studentQuery = DB::table('student_personal_information')
            ->where('email', $value);

        // Exclude the current record if ID is provided
        if ($this->id) {
            $studentQuery->where('id', '!=', $this->id);
        }

        if ($studentQuery->exists()) {
            $fail('The email has already been taken in students table.');
            return;
        }

        // Check uniqueness in users table
        $userQuery = DB::table('users')
            ->where('email', $value)
            ->where('id', '!=', $userId);

        if ($userQuery->exists()) {
            $fail('The email has already been taken in users table.');
            return;
        }
    }
}
