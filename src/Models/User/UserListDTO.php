<?php
declare(strict_types=1);

namespace Models\User;

final class UserListDTO
{

	public function __construct
	(
		public readonly int $id,
		public readonly string $name,
		public readonly string $phone,
		public readonly string $email,
		public readonly string $role
	) {}

}