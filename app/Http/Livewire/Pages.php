<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\Page;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Pages extends Component
{
    use WithPagination;

    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;
    public $slug;
    public $title;
    public $content;
    public $modelId;
    public $isSetToDefaultHomePage;
    public $isSetToDefaultNotFoundPage;

    public function rules(){
        return [
            'title' => 'required',
            'slug' => ['required', Rule::unique('pages', 'slug')->ignore($this->modelId)],
            'content' => 'required'
        ];
    }

    public function mount(){
        $this->resetPage();
    }

    public function updatedTitle($value){
        $this->slug = Str::slug($value);
    }

    public function updatedIsSetToDefaultHomePage(){
        $this->isSetToDefaultNotFoundPage = null;
    }
    
    public function updatedIsSetToDefaultNotFoundPage(){
        $this->isSetToDefaultHomePage = null;
    }

    public function createShowModal(){
        $this->resetValidation();
        $this->reset();
        $this->modalFormVisible = true;
    }

    public function updateShowModal($id){
        $this->resetValidation();
        $this->reset();
        $this->modelId = $id;
        $this->modalFormVisible = true;
        $this->loadModel();
    }

    public function deleteShowModal($id){
        $this->modelId = $id;
        $this->modalConfirmDeleteVisible = true;
    }

    public function loadModel(){
        $data = Page::where('id', $this->modelId)->first();
        $this->title = $data->title;
        $this->slug = $data->slug;
        $this->content = $data->content;
        $this->isSetToDefaultHomePage = !$data->is_default_home ? null:true;
        $this->isSetToDefaultNotFoundPage = !$data->is_default_not_found ? null:true;
    }

    public function create(){
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFound();
        Page::create($this->modelData());
        $this->modalFormVisible = false;
        $this->reset();
    }

    public function read(){
        return Page::paginate(5);
    }

    public function update(){
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFound();
        Page::where('id', $this->modelId)->update($this->modelData());
        $this->modalFormVisible = false;
    }

    public function delete(){
        Page::where('id', $this->modelId)->delete();
        $this->modalConfirmDeleteVisible = false;
        $this->resetPage();
    }

    public function modelData(){
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'is_default_home' => $this->isSetToDefaultHomePage,
            'is_default_not_found' => $this->isSetToDefaultNotFoundPage
        ];
    }

    private function generateSlug($value){
        $process1 = str_replace(' ', '-', $value);
        $process2 = strtolower($process1);
        $this->slug = $process2;
    }
    
    private function unassignDefaultHomePage(){
        if($this->isSetToDefaultHomePage != null){
            Page::where('is_default_home', true)->update([
                'is_default_home' => false,
            ]);
        }
    }

    private function unassignDefaultNotFound(){
        if($this->isSetToDefaultNotFoundPage != null){
            Page::where('is_default_not_found', true)->update([
                'is_default_not_found' => false,
            ]);
        }
    }

    public function dispatchEvent(){
        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Sample Event',
            'eventMessage' => 'You have a sample event notification.',
        ]);
    }
    
    public function render()
    {
        return view('livewire.pages', ['data' => $this->read()]);
    }
}
