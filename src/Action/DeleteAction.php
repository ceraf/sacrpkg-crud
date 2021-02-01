<?php

namespace sacrpkg\CrudBundle\Action;

class DeleteAction extends ActionAbstract
{
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

                    } catch (Exception $e) {
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
