<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TourService
{
  /**
   * SA／システム管理者を取得
   * @return Object
   */
  public function get_managers()
  {
    $user_type_list = '1,9';

    if (!Session::has('authority'))
    {
      return [];
    }
    else
    {
      if (
        Session::get('authority.user_type') != 1
        &&
        Session::get('authority.user_type') != 9
      )
      {
        return [];
      }
    }

    $sql = "
SELECT 
  * 
FROM 
  users 
WHERE 
  user_type IN ({$user_type_list})
";

    $result = DB::Select($sql);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

  /**
   * SA／管理者ユーザを追加する
   * @param Object $params
   * @return Object
   */
  public function insert_manager($params)
  {
    $check = DB::table('users')->where([
      ['email', $params['email']]
    ])->first();

    if (count($check) > 0)
    {
      return array(
          'count' => 0
        , 'error' => 'すでに存在するメールアドレスです。'
      );
    }

    $now = date('Y/m/d H:i:s');

    $params['valid_start_on']   = $params['valid_start_on'] ? $params['valid_start_on'] : null;
    $params['valid_end_on']     = $params['valid_end_on'] ? $params['valid_end_on'] : null;
    $params['created_user_id']  = session()->get('authority.id');
    $params['created_at']       = $now;
    $params['updated_user_id']  = session()->get('authority.id');
    $params['updated_at']       = $now;

//    DB::enableQueryLog();
    $count = DB::table('users')->insert([$params]);
//    dd(DB::getQueryLog());
    return array('count' => $count);
  }

  /**
   * SA／管理者ユーザを更新する
   * @param Object $params
   * @return Object
   */
  public function update_manager($params)
  {
    $check = DB::table('users')->where([
      ['id', '<>', $params['id']],
      ['email', $params['email']]
    ])->first();

    if (count($check) > 0)
    {
      return array(
          'count' => 0
        , 'error' => 'すでに存在するメールアドレスです。'
      );
    }

    $sql = "
UPDATE
  users
SET
    last_name       = :last_name
  , first_name      = :first_name
  , email           = :email
  , place_code      = :place_code
  , staff_code      = :staff_code
  , valid_start_on  = :valid_start_on
  , valid_end_on    = :valid_end_on
  , updated_user_id = :updated_user_id
  , updated_at      = CURRENT_TIMESTAMP
WHERE
  id = :id
";

    $array = array(
        'id'              => $params['id']
      , 'last_name'       => $params['last_name']
      , 'first_name'      => $params['first_name']
      , 'email'           => $params['email']
      , 'place_code'      => $params['place_code']
      , 'staff_code'      => $params['staff_code']
      , 'valid_start_on'  => ($params['valid_start_on'] ? $params['valid_start_on'] : null)
      , 'valid_end_on'    => ($params['valid_end_on'] ? $params['valid_end_on'] : null)
      , 'updated_user_id' => session()->get('authority.id')
    );

    $count = DB::update($sql, $array);

    return array('count' => $count);
  }

  /**
   * 主催者（管理者向け）を取得
   * @return Object
   */
  public function get_organizers()
  {
    $sql = "
SELECT 
  * 
FROM 
  organizers 
";
    $result = DB::Select($sql);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

  /**
   * 主催者（SA向け）を取得
   * @param String $place_code - 箇所コード
   * @return Object
   */
  public function get_organizers_for_sa($place_code)
  {
    $sql = "
SELECT 
  * 
FROM 
  organizers 
WHERE 
  organizer_code in (
    SELECT 
      t.organizer_code 
    FROM 
      tours t 
    WHERE 
      t.place_code = ?  
  )
";
    $result = DB::Select($sql, [$place_code]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }


  /**
   * 全ツアーを取得（管理者向け）
   * @return Object
   */
  public function get_tours_all()
  {
    $sql = "
SELECT 
  t.* 
FROM 
  tours t 
";
    $result = DB::Select($sql, [$organizer_code]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

  /**
   * ツアーを取得（※未使用）
   * @param String $organizer_code - 主催者コード
   * @return Object
   */
  public function get_tours($organizer_code)
  {
    $sql = "
SELECT 
  t.* 
FROM 
  tours t 
WHERE 
  t.organizer_code = ? 
";
    $result = DB::Select($sql, [$organizer_code]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

  /**
   * ツアーを取得（SA向け）
   * @param String $organizer_code - 主催者コード
   * @param String $place_code - 箇所コード
   * @return Object
   */
  public function get_tours_for_sa($organizer_code, $place_code)
  {
    $sql = "
SELECT 
  t.* 
FROM 
  tours t 
WHERE 
  t.place_code = ? 
";
    $result = DB::Select($sql, [$place_code]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

  /**
   * ツアーを取得（主催者向け）
   * @param String $organizer_code - 主催者コード
   * @param String $section - 所属
   * @return Object
   */
  public function get_tours_for_organizer($organizer_code, $section)
  {
    $sql = "
SELECT 
  t.* 
FROM 
  tours t 
INNER JOIN browsing_range br 
   ON br.course_id = t.course_id 
WHERE 
  t.organizer_code = ? 
AND 
  br.section = ? 
";
    $result = DB::Select($sql, [$organizer_code, $section]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $key => $val)
      {
        $array[$key] = $val;
      }
    }

    return $array;
  }

////////////////////////////////////////////////////////////////////////////////

  /**
   * 申込（所属単位）を取得
   * @param String $course_id - コースID
   * @return Object
   */
  public function get_applications_group_by_section($course_id)
  {
    $summary = array();

    // 申込ありの所属
    $sql = "
SELECT 
  IFNULL(a.applicant_section, '-') AS applicant_section 
FROM 
  applications a 
WHERE 
  a.course_id = :course_id 
AND  
  a.is_newest = 1 
AND 
  a.is_enabled = 1 
GROUP BY 
  a.applicant_section 
";

    $sections = DB::Select($sql, ['course_id' => $course_id]);
    foreach ($sections as $section)
    {
      $summary[$section->applicant_section] = array(
          'participant' => 0
        , 'menu'        => 0
        , 'traffic'     => 0
        , 'option'      => 0
      );
    }

    // 参加者
    $sql = "
SELECT
  IFNULL(a.applicant_section, '-') AS applicant_section 
  , count(ap.participant_id) as summary_participant 
FROM
  applications a 
  INNER JOIN application_participants ap 
    ON ap.application_id = a.application_id 
    AND ap.branch_no = a.branch_no 
    AND ap.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
  a.applicant_section
";

    $participants = DB::Select($sql, [$course_id]);
    if (count($participants) > 0 )
    {
      foreach ($participants as $participant)
      {
        $summary[$participant->applicant_section]['participant'] = $participant->summary_participant;
      }
    }

    // 詳細
    $categorys = ['menu', 'traffic', 'option'];
    foreach ($categorys as $category)
    {
      $sql = "
SELECT
  IFNULL(a.applicant_section, '-') AS applicant_section 
  , ad.category as category 
  , count(ad.id) as cnt 
FROM
  applications a 
  INNER JOIN application_details ad 
    ON ad.application_id = a.application_id 
    AND ad.branch_no = a.branch_no 
    AND ad.category = :category 
    AND ad.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.applicant_section 
  , ad.category
";

      $details = DB::Select($sql, ['course_id' => $course_id, 'category' => $category]);
      foreach ($details as $detail)
      {
        $summary[$detail->applicant_section][$detail->category] = $detail->cnt;
      }
    }

    // 手配
    $sql = "
SELECT
  IFNULL(a.applicant_section, '-') AS applicant_section 
  , count(ar.id) as cnt 
FROM
  applications a 
  INNER JOIN application_reserves ar 
    ON ar.application_id = a.application_id 
    AND ar.branch_no = a.branch_no 
    AND ar.is_enabled = 1 
    AND ar.reserved_is_reserved = 1 
WHERE
  a.course_id = :course_id 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.applicant_section 
";

    $reserves = DB::Select($sql, ['course_id' => $course_id]);

    foreach ($reserves as $reserve)
    {
      $summary[$detail->applicant_section]['traffic'] += $reserve->cnt;
    }

    return $summary;
  }

////////////////////////////////////////////////////////////////////////////////

  /**
   * 申込（所属×申込者単位）を取得
   * @param String $course_id - コースID
   * @param String $section - 所属
   * @return Object
   */
  public function get_applications_group_by_applicant($course_id, $section)
  {
    //$summary = (object) array();
    $summary = (object)[];

    // 申込ありの代表者
    if ($section != '-')
    {
      $sql = "
SELECT 
    a.application_id 
  , a.applicant_first_name 
  , a.applicant_last_name 
  , a.applicant_user_id 
  , a.is_enabled
FROM 
  applications a 
WHERE 
  a.course_id = :course_id 
AND 
  a.applicant_section = :section 
AND
  a.is_newest = 1 
";
    $applicants = DB::Select($sql, ['course_id' => $course_id, 'section' => $section]);
  }
  else
  {
    $sql = "
SELECT 
    a.application_id 
  , a.applicant_first_name 
  , a.applicant_last_name 
  , a.applicant_user_id 
  , a.is_enabled
FROM 
  applications a 
WHERE 
  a.course_id = :course_id 
AND
  (a.applicant_section IS NULL OR a.applicant_section = '')
AND
  a.is_newest = 1 
";
    $applicants = DB::Select($sql, ['course_id' => $course_id]);
  }

    foreach ($applicants as $applicant)
    {
      $application_id = $applicant->application_id; 
      $summary->$application_id['applicant_first_name'] = $applicant->applicant_first_name;
      $summary->$application_id['applicant_last_name'] = $applicant->applicant_last_name;
      $summary->$application_id['applicant_user_id'] = $applicant->applicant_user_id;
      $summary->$application_id['participant'] = 0;
      $summary->$application_id['menu'] = 0;
      $summary->$application_id['traffic'] = 0;
      $summary->$application_id['option'] = 0;
    }

    // 参加者
    if ($section != '-')
    {
      $sql = "
SELECT
  a.application_id
  , count(ap.participant_id) as summary_participant 
FROM
  applications a 
  INNER JOIN application_participants ap 
    ON ap.application_id = a.application_id 
    AND ap.branch_no = a.branch_no 
    AND ap.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section = :section 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
  a.application_id
";
      $participants = DB::Select($sql, ['course_id' => $course_id, 'section' => $section]);
    }
    else
    {
      $sql = "
SELECT
  a.application_id
  , count(ap.participant_id) as summary_participant 
FROM
  applications a 
  INNER JOIN application_participants ap 
    ON ap.application_id = a.application_id 
    AND ap.branch_no = a.branch_no 
    AND ap.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section IS NULL 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
  a.application_id
";
      $participants = DB::Select($sql, ['course_id' => $course_id]);
    }

    if (count($participants) > 0 )
    {
      foreach ($participants as $participant)
      {
        $application_id = $participant->application_id; 
        $summary->$application_id['participant'] = $participant->summary_participant;
      }
    }

    // 詳細
    $categorys = ['menu', 'traffic', 'option'];
    foreach ($categorys as $category)
    {
      if ($section != '-')
      {
        $sql = "
SELECT
  a.application_id 
  , ad.category as category 
  , count(ad.id) as cnt 
FROM
  applications a 
  INNER JOIN application_details ad 
    ON ad.application_id = a.application_id 
    AND ad.branch_no = a.branch_no 
    AND ad.category = :category 
    AND ad.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section = :section 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.application_id 
  , ad.category
";
        $details = DB::Select($sql, ['course_id' => $course_id, 'section' => $section, 'category' => $category]);
      }
      else
      {
        $sql = "
SELECT
  a.application_id 
  , ad.category as category 
  , count(ad.id) as cnt 
FROM
  applications a 
  INNER JOIN application_details ad 
    ON ad.application_id = a.application_id 
    AND ad.branch_no = a.branch_no 
    AND ad.category = :category 
    AND ad.is_enabled = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section IS NULL 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.application_id 
  , ad.category
";
        $details = DB::Select($sql, ['course_id' => $course_id, 'category' => $category]);
      }
        
      foreach ($details as $detail)
      {
        $application_id = $detail->application_id; 
        $summary->$application_id[$detail->category] = $detail->cnt;
      }
    }

    // 手配
    if ($section != '-')
    {
      $sql = "
SELECT
  a.application_id 
  , count(ar.id) as cnt 
FROM
  applications a 
  INNER JOIN application_reserves ar 
    ON ar.application_id = a.application_id 
    AND ar.branch_no = a.branch_no 
    AND ar.is_enabled = 1 
    AND ar.reserved_is_reserved = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section = :section 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.application_id 
";
      $reserves = DB::Select($sql, ['course_id' => $course_id, 'section' => $section]);
    }
    else
    {
      $sql = "
SELECT
  a.application_id 
  , count(ar.id) as cnt 
FROM
  applications a 
  INNER JOIN application_reserves ar 
    ON ar.application_id = a.application_id 
    AND ar.branch_no = a.branch_no 
    AND ar.is_enabled = 1 
    AND ar.reserved_is_reserved = 1 
WHERE
  a.course_id = :course_id 
  AND a.applicant_section IS NULL 
  AND a.is_newest = 1 
  AND a.is_enabled = 1 
GROUP BY
    a.application_id 
";
      $reserves = DB::Select($sql, ['course_id' => $course_id]);
    }

    foreach ($reserves as $reserve)
    {
      $application_id = $reserve->application_id; 
      $summary->$application_id['traffic'] += $reserve->cnt;
    }

    return $summary;
  }

}
