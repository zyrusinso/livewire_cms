<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\NavigationMenu as Menu;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class NavigationMenus extends Component
{
    use Withpagination;

    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;

    public $modelId;
    public $label;
    public $slug;
    public $sequence = 1;
    public $type = "SidebarNav";

    public function rules(){
        return [
            'label' => 'required',
            'slug' => 'required',
            'sequence' => 'required',
            'type' => 'required',
        ];
    }

    public function create(){
        $this->validate();
        Menu::create($this->modelData());
        $this->modalFormVisible = false;
        $this->reset();
    }
    
    public function read(){
        return Menu::paginate(5);
    }

    public function update(){
        $this->validate();
        Menu::where('id', $this->modelId)->update($this->modelData());
        $this->modalFormVisible = false;
    }

    public function delete(){
        Menu::where('id', $this->modelId)->delete();
        $this->modalConfirmDeleteVisible = false;
    }

    public function createShowModal(){
        $this->resetValidation();
        $this->reset();
        $this->modalFormVisible = true;
    }

    public function updateShowModal($id){
        $this->resetValidation();
        $this->reset();
        $this->modalFormVisible = true;
        $this->modelId = $id;
        $this->loadModel();
    }

    public function deleteShowModal($id){
        $this->modelId = $id;
        $this->modalConfirmDeleteVisible = true;
    }

    public function loadModel(){
        $data = Menu::where('id', $this->modelId)->first();
        $this->label = $data->label;
        $this->slug = $data->slug;
        $this->type = $data->type;
        $this->sequence = $data->sequence;
    }
    
    public function modelData(){
        return [
            'label' => $this->label,
            'slug' => $this->slug,
            'sequence' => $this->sequence,
            'type' => $this->type
        ];
    }

    public function render()
    {
        return view('livewire.navigation-menus', ['data'=> $this->read()]);
    }
}
