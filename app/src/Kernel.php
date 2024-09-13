<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Class Kernel.
 *
 * The main application kernel, extending the Symfony base kernel and utilizing the MicroKernelTrait
 * for bootstrapping and configuration of the Symfony application.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
