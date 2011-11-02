<?php



/**
 * Skeleton subclass for performing query and update operations on the 'branch' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.5.6 on:
 *
 * Mon Oct 24 09:36:19 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class BranchPeer extends BaseBranchPeer {

  const A_TRAITER = 0;
  const OK        = 1;
  const KO        = 2;

  /**
   * @static
   * @param int $statusId
   * @return string
   */
  public static function getLabelStatus($statusId)
  {
    switch ($statusId)
    {
      case BranchPeer::A_TRAITER:
        return 'to do';
      case BranchPeer::OK:
        return 'ok';
      case BranchPeer::KO:
        return 'ko';
    }

    return '';
  }
  
  /**
   * @static
   * @param Repository $repository
   * @return void
   */
  public static function synchronize(Repository $repository)
  {
    $branchesGit = GitCommand::getNoMergedBranchesInfos($repository->getValue());

    $branchesModel = BranchQuery::create()
      ->filterByRepositoryId($repository->getId())
      ->find()
    ;

    foreach ($branchesModel as $branchModel)
    {
      if (!array_key_exists($branchModel->getName(), $branchesGit))
      {
        $branchModel->delete();
      }
      else
      {
        if(!$branchModel->getCommitReference())
        {
          $branchModel->setCommitReference($branchesGit[$branchModel->getName()]['commit_reference']);
          $branchModel->setLastCommit($branchesGit[$branchModel->getName()]['last_commit']);
          $branchModel->save();
        }
        unset($branchesGit[$branchModel->getName()]);
      }
    }

    foreach ($branchesGit as $name => $branchGit)
    {
      $branch = new Branch();
      $branch->setName($name)
        ->setStatus(BranchPeer::A_TRAITER)
        ->setRepositoryId($repository->getId())
        ->setCommitReference($branchGit['commit_reference'])
        ->setLastCommit($branchGit['last_commit'])
        ->save()
      ;
    }
  }
} // BranchPeer
