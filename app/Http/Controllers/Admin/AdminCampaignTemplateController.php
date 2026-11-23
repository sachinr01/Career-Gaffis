<?php

namespace App\Http\Controllers\Admin;

use App\CampaignTemplate;
use App\Helper\Reply;
use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCampaignTemplateController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.campaignTemplate');
        $this->pageIcon = 'icon-docs';
    }

    public function index()
    {
        abort_if(!$this->user->cans('view_campaign_template'), 403);

        $templates = CampaignTemplate::orderByDesc('id')->get();

        $this->data['templates'] = $templates;

        return view('admin.campaign-template.index', $this->data);
    }

    public function create()
    {
        abort_if(!$this->user->cans('add_campaign_template'), 403);

        return view('admin.campaign-template.create', $this->data);
    }

  public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
       // 'slug' => 'required|string|unique:campaign_templates,slug',
        'html' => 'nullable',
        'css' => 'nullable',
        'preview' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = $request->except(['preview']);
    $data['owner_id'] = auth()->id(); // Set owner_id from authenticated user

    if ($request->hasFile('preview')) {
        $file = $request->file('preview');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/campaign-previews');

        // Create folder if not exists
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);
        $data['preview'] = 'uploads/campaign-previews/' . $filename;
    }

    CampaignTemplate::create($data);

    return redirect()->route('admin.campaign-template.index')->with('success', 'Campaign template created successfully.');
}



    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_campaign_template'), 403);

        $template = CampaignTemplate::findOrFail($id);
        $this->data['template'] = $template;

        return view('admin.campaign-template.edit', $this->data);
    }

public function update(Request $request, $id)
{
    $template = CampaignTemplate::findOrFail($id);

    $request->validate([
        'title' => 'required|string|max:255',
       // 'slug' => 'required|string|max:255|unique:campaign_templates,slug,' . $id,
        'html' => 'nullable',
        'css' => 'nullable',
        'preview' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = $request->except(['preview']);
    $data['owner_id'] = auth()->id(); // Set owner_id from authenticated user

    if ($request->hasFile('preview')) {
        $file = $request->file('preview');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/campaign-previews');

        // Create folder if not exists
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);
        $data['preview'] = 'uploads/campaign-previews/' . $filename;

        // Optionally delete old preview image
        if ($template->preview && file_exists(public_path($template->preview))) {
            unlink(public_path($template->preview));
        }
    }

    $template->update($data);

    return redirect()->route('admin.campaign-template.index')->with('success', 'Campaign template updated successfully.');
}

    public function destroy($id)
    {
        abort_if(!$this->user->cans('delete_campaign_template'), 403);

        CampaignTemplate::destroy($id);

        return redirect()->route('admin.campaign-template.index')->with('success', 'Template deleted.');
    }
}
