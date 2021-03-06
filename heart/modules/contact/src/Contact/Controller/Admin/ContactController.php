<?php

namespace Contact\Controller\Admin;

use Contact\Model\Mail as Mail;
use Event, Flash, Input, Pagination, Redirect, Translate;

class ContactController extends \AdminController
{
    public function before()
    {
        $this->menu->activeParent(\Module::get('contact', 'uri'));
        $this->template->style('contact.css', 'contact');
    }

    /**
     * Show all Email to Admin
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function index()
    {

        $options = array(
            'total_items'	=> Mail::count(),
            'items_per_page'=> \Setting::get('admin_item_per_page'),
            );

        $pagination = Pagination::create($options);

        if (Pagination::isInvalid()) {
            return $this->notFound();
        }

        $result = Mail::skip(Pagination::offset())
                            ->take(Pagination::limit())
                            ->orderBy('id','desc')
                            ->get();

        $this->template->title(Translate::get('contact::contact.inbox'))
                    ->breadcrumb(Translate::get('contact::contact.all_con'))
                    ->set('mails', $result)
                    ->set('pagination',$pagination)
                    ->view('admin\inbox\index');
    }

    /**
     * Show detail Email to Admin
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function detail($id)
    {
        if (!user_has_access('contact.view')) return $this->notFound();
        $mail = Mail::where('id', '=', $id)->first();

        if (count($mail) == 0) return $this->notFound();

        $mail->read_mail = 1;
        $mail->save();

        Event::call('email_receive_detail',array($mail));
        $temp = array();
        if (\Module::isEnabled('field')) {

                $temp = \Field::get('contact', $mail);

            }

        $this->template->title(Translate::get('contact::contact.title'))
                    ->breadcrumb($mail->subject)
                    ->set('mail',$mail)
                    ->set('field',$temp->extended_fields)
                    ->view('admin\inbox\detail');
    }

    /**
     * Delete Email from Database
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function delete($id = 0)
    {
        if (!user_has_access('contact.delete')) return $this->notFound();
        $ids = ($id) ? array($id) : Input::get('action_to');

        $mails = array();

        foreach ($ids as $id) {
            if ($mail = Mail::find($id)) {
                if ($mail->delete()) {
                    if (\Module::isEnabled('field')) {

                        \Field::delete('contact', $mail);

                    }
                }
                $mails[] = "success";
            }
        }

        if (!empty($mails)) {
            if (count($mails) == 1) {
                Flash::success(Translate::get('contact::contact.mail_delete'));
            } else {
                Flash::success(Translate::get('contact::contact.mails_delete'));
            }
        } else {
            Flash::error(Translate::get('contact::contact.template_error'));
        }
        Event::call('email_receive_delete', array(true));

        return Redirect::toAdmin('contact');
    }

}
