<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Action;

/**
 * Delete action for more items.
 */
class DeleteAction extends ActionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function execute($params = [])
    {
        $id = $params['id'];
		if ($this->request->getMethod() == 'POST') {
			if ($id) {
				$row = $this->em
						->getRepository($this->entity)
						->find($id);
				if ($row) {
					try {       				
						$this->em->getConnection()->beginTransaction();
                        $this->beforeDeleteExecute($this->em, $row);
						$this->em->remove($row);
						$this->em->flush();
                        $this->flashMessage('notice', 'Запись удалена.');
						$this->em->getConnection()->commit();

                    } catch (\Exception $e) {
						$this->flashMessage('error', 'При удалении произошла ошибка.');
						$this->em->getConnection()->rollback();
					}
				} else {
					$this->flashMessage('error', 'При удалении произошла ошибка.');
				}
			}
        }
        
		return $this->redirectToRoute($this->homeroute);
    }
}
