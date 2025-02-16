<?php

namespace App\Services;

use \Illuminate\Database\Eloquent\Model;

class TreeService
{
    protected $roots;
    protected $items;
    protected $children = [];
    protected $parents;

    public function add(Model $row): void {
        $this->items[$row->id] = $row;

        if ($row->pid) {
            $this->children[$row->pid][] = $row->id;
            $this->parents[$row->id] = $row->pid;
        } else {
            $this->roots[] = $row->id;
        }
        /*echo "<pre>";
        var_dump($group->pid);
        var_dump($this->roots);
        var_dump($group);
        die;*/
    }

    public function hasChildren(int $id): bool {
        return $this->children && array_key_exists($id, $this->children) && $this->children[$id];
    }

    public function getChildrenDeep(int $id): array {
        $ret = [ $id ];
        
        $children = $this->getChildren($id);
        // dd($children);
        foreach ($children as $child) {
            $ret = array_merge($ret, $this->getChildrenDeep($child->id));
        }
        return $ret;
    }
    
    public function getChildren(int $id): array {
        if (!array_key_exists($id, $this->children )) {
            return [];
        }

        return $this->getItemsByIds($this->children[$id]);
    }

    public function getItemsByIds(?array $ids): ?array {
        $ret = [];

        if ($ids) {
            foreach ($ids as $id) {
                $ret[$id] = $this->getItemById($id);
            }
        }

        return $ret;
    }

    public function getItemById(int $id): Model {
        if (!array_key_exists($id, $this->items)) {
            $mes = sprintf("No %u key in items array");
            throw new DataConflictException($mes);
        }

        return $this->items[$id];
    }

    public function getRoots(): ?array {
        return $this->getItemsByIds($this->getRootIds());
    }

    public function getRootIds(): ?array {
        return $this->roots;
    }

    public function getParent(int $id): ?Model {
        $parentId = $this->getParentId($id);

        if (!$parentId) {
            return null;
        }

        return $this->getItemById($parentId);
    }

    public function getParentId(int $id): int {
        return $this->parents && array_key_exists($id, $this->parents)
            ? $this->parents[$id]
            : 0;
    }

    public function getTreeItem(int $id, int $level = 1): array {
        $children = [];
        $item = $this->getItemById($id);
        
        if ($this->hasChildren($id)) {
            foreach ($this->getChildren($id) as $child) {
                $children[] = $this->getTreeItem($child->id, $level + 1);
            }
        }
        
        return [
            'level' => $level,
            'item' => $item,
            'children' => $children
        ];
    }
    
    public function getTree(): array {
        $tree = [];
        
        foreach($this->getRootIds() as $root) {
            $tree[] = $this->getTreeItem($root);
        }
        
        return $tree;
    }
    
    public function getParents(int $id): array {
        $ret = [];

        $parent = $this->getParent($id);

        while ($parent) {
            $ret[] = $parent;
            $parent = $this->getParent($parent->id);
        }

        return array_reverse($ret);
    }

    public function getItemIds(): array {
        return $this->items ? array_keys($this->items) : [];
    }
    
    public static function buildFromQuery(\Illuminate\Database\Eloquent\Builder $q): TreeService {
        $rows = $q->get();

        $ts = new TreeService();
        
        foreach ($rows as $row) {
            $ts->add($row);
        }
        
        return $ts;
    }
}
