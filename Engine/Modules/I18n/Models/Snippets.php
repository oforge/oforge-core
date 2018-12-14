<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:15
 */

namespace Oforge\Engine\Modules\I18n\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_i18n_snippets")
 * @ORM\Entity
 */
class Snippets extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="scope", type="string", nullable=false)
     */
    private $scope;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="valuie", type="string", nullable=false)
     */
    private $value;
}
