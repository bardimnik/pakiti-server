<?php
# Copyright (c) 2017, CESNET. All rights reserved.
#
# Redistribution and use in source and binary forms, with or
# without modification, are permitted provided that the following
# conditions are met:
#
#   o Redistributions of source code must retain the above
#     copyright notice, this list of conditions and the following
#     disclaimer.
#   o Redistributions in binary form must reproduce the above
#     copyright notice, this list of conditions and the following
#     disclaimer in the documentation and/or other materials
#     provided with the distribution.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
# CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
# INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
# MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
# BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
# EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
# ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
# OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
# OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

/**
 * @author Vadym Yanovskyy
 */
class CveDao
{
    private $db;

    public function __construct(DbManager &$dbManager)
    {
        $this->db = $dbManager;
    }

    /*
     * Stores the Cve in the DB
     */
    public function create(Cve &$cve)
    {
        $this->db->query(
            "insert into Cve set
      	name='" . $this->db->escape($cve->getName()) . "',
      	cveDefId='" . $this->db->escape($cve->getCveDefId()) . "'");

        # Set the newly assigned id
        $cve->setId($this->db->getLastInsertedId());
    }

    public function getCve($name, $cveDefId)
    {
        return $this->db->queryObject(
            "select
    		id as _id, name as _name, cveDefId as _cveDefId
      from
      	Cve
      where
      	name='" . $this->db->escape($name) . "' AND
      	cveDefId ='" . $this->db->escape($cveDefId) . "'", "Cve");
    }

    public function getCvesByName($name)
    {
        return $this->db->queryObjects(
            "select
    		id as _id, name as _name, cveDefId as _cveDefId
            from
      	      Cve
            where
              name='" . $this->db->escape($name) . "'", "Cve");
    }

    public function getCveNameById($cveId)
    {
        return $this->db->queryToSingleValue(
            "select name from Cve
              where id='" . $this->db->escape($cveId) . "'"
        );
    }

    public function getCvesByCveDefId($cveDefId)
    {
        return $this->db->queryObjects("select id
        as _id, name as _name, cveDefId
        as _cveDefId from Cve where
        Cve.cveDefId={$cveDefId}", "Cve");
    }

    public function getCvesByCveDef(CveDef $cveDef)
    {
        return $this->getCvesByCveDefId($cveDef->getId());
    }

    public function getCveNames()
    {
        $sql = "select distinct Cve.name from Cve
            order by Cve.name DESC";
        return $this->db->queryToSingleValueMultiRow($sql);
    }

    public function getUsedCveNames()
    {
        $sql = "select distinct Cve.name from Cve
            inner join PkgCveDef on Cve.cveDefId = PkgCveDef.cveDefId
            order by Cve.name DESC";
        return $this->db->queryToSingleValueMultiRow($sql);
    }

    public function getAllCves()
    {
        return $this->db->queryObjects(
            "select
    		id as _id, name as _name, cveDefId as _cveDefId
            from
      	      Cve", "Cve");
    }
}
