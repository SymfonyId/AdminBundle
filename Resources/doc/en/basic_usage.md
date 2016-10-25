### Basic Usage###

1. **Create Your Entity**

    ```lang=php
    <?php
    namespace AppBundle\Entity;

    /**
     * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
     * Url: http://blog.khodam.org
     */

    use Doctrine\ORM\Mapping as ORM;
    use Symfonian\Indonesia\AdminBundle\Grid\Column;
    use Symfonian\Indonesia\AdminBundle\Grid\Filter;
    use Symfonian\Indonesia\AdminBundle\Contract\BulkDeletableInterface;
    use Symfonian\Indonesia\AdminBundle\Contract\EntityInterface;

    /**
     * @ORM\Entity
     * @ORM\Table(name="siab_idname")
     */
    class IdName implements EntityInterface, BulkDeletableInterface
    {
        /**
         * @ORM\Id
         * @ORM\Column(name="id", type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @Column()
         * @Filter()
         * @ORM\Column(name="program_name", type="string", length=77)
         */
        protected $name;

        public function getId()
        {
            return $this->id;
        }

        public function setName($name)
        {
            $this->name = strtoupper($name);

            return $this;
        }

        public function getName()
        {
            return $this->name;
        }

        /**
         * @return string
         */
        public function getDeleteInformation()
        {
            return $this->getName();
        }
    }
    ```

2. **Running the generator**
    The command below automaticly generate `Form Type` and `Controller` for your entity, and also add your entity to menu.

    ```lang=shell
    php bin/console siab:generator:crud AppBundle:IdName --overwrite
    ```
---

[Next: Plugin Activation](plugin_activation.md)
