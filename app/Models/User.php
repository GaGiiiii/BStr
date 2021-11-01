<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var string[]
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'password',
    'image',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function comments() {
    return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
  }

  public function likes() {
    return $this->hasMany(Like::class)->orderBy('created_at', 'DESC');
  }

  public function posts() {
    return $this->hasMany(Post::class)->orderBy('created_at', 'DESC');
  }

  public function interests() {
    return $this->hasMany(Interest::class);
  }
}
