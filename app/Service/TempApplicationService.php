<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SequenceController;
use App\Service\ApplicationService;

use App\User;

class TempApplicationService
{
  /**
   * 一時在庫（全て）削除
   * @param string $application_id - 申込ID
   */
  public function drop_temp_all($application_id)
  {
    DB::beginTransaction();

    try
    {
      $sql = "
DELETE FROM
  application_temp_details
WHERE
  application_id = :application_id
";
      $array = array(
        'application_id'  => $application_id
      );

      //DB::enableQueryLog();
      $result = DB::delete($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（宿泊）削除
   * @param string $application_id - 申込ID
   */
  public function drop_temp_inn($application_id)
  {
    $category = 'inn';

    DB::beginTransaction();

    try
    {
      $sql = "
DELETE FROM
  application_temp_details
WHERE
  application_id = :application_id
AND
  category = :category
";
      $array = array(
          'application_id'  => $application_id
        , 'category'        => $category
      );

      //DB::enableQueryLog();
      $result = DB::delete($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（交通）削除
   * @param string $application_id - 申込ID
   */
  public function drop_temp_traffic($application_id)
  {
    $category = 'traffic';

    DB::beginTransaction();

    try
    {
      $sql = "
DELETE FROM
  application_temp_details
WHERE
  application_id = :application_id
AND
  category = :category
";
      $array = array(
          'application_id'  => $application_id
        , 'category'        => $category
      );

      //DB::enableQueryLog();
      $result = DB::delete($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（オプション）削除
   * @param string $application_id - 申込ID
   */
  public function drop_temp_option($application_id)
  {
    $category = 'option';

    DB::beginTransaction();

    try
    {
      $sql = "
DELETE FROM
  application_temp_details
WHERE
  application_id = :application_id
AND
  category = :category
";
      $array = array(
          'application_id'  => $application_id
        , 'category'        => $category
      );

      //DB::enableQueryLog();
      $result = DB::delete($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（自由項目）削除
   * @param string $application_id - 申込ID
   */
  public function drop_temp_etc($application_id)
  {
    $category = 'etc';

    DB::beginTransaction();

    try
    {
      $sql = "
DELETE FROM
  application_temp_details
WHERE
  application_id = :application_id
AND
  category = :category
";
      $array = array(
          'application_id'  => $application_id
        , 'category'        => $category
      );

      //DB::enableQueryLog();
      $result = DB::delete($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（宿泊）登録
   * @param array $params - SQLパラメータ
   */
  public function regist_temp_inn($params)
  {
    $category = 'inn';

    DB::beginTransaction();

    try
    {
      $sql = "
INSERT INTO application_temp_details
(
    course_id
  , application_id
  , category
  , inn_room_no
  , quantity
  , target_on
  , expiration_at
)
VALUES
(
    :course_id
  , :application_id
  , :category
  , :inn_room_no
  , :quantity
  , :target_on
  , (NOW() + INTERVAL 1 HOUR)
)
";

      $array = array(
          'course_id'       => $params['course_id']
        , 'application_id'  => $params['application_id']
        , 'category'        => $category
        , 'inn_room_no'     => $params['inn_room_no']
        , 'target_on'       => $params['target_on']
        , 'quantity'        => $params['quantity']
      );

      //DB::enableQueryLog();
      $result = DB::insert($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（交通）登録
   * @param array $params - SQLパラメータ
   */
  public function regist_temp_traffic($params)
  {
    $category = 'traffic';

    DB::beginTransaction();

    try
    {
      $sql = "
INSERT INTO application_temp_details
(
    course_id
  , application_id
  , category
  , traffic_no
  , quantity
  , expiration_at
)
VALUES
(
    :course_id
  , :application_id
  , :category
  , :traffic_no
  , :quantity
  , (NOW() + INTERVAL 1 HOUR)
)
";

      $array = array(
          'course_id'       => $params['course_id']
        , 'application_id'  => $params['application_id']
        , 'category'        => $category
        , 'traffic_no'      => $params['traffic_no']
        , 'quantity'        => $params['quantity']
      );

      //DB::enableQueryLog();
      $result = DB::insert($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（オプション）登録
   * @param array $params - SQLパラメータ
   */
  public function regist_temp_option($params)
  {
    $category = 'option';

    DB::beginTransaction();

    try
    {
      $sql = "
INSERT INTO application_temp_details
(
    course_id
  , application_id
  , category
  , option_no
  , quantity
  , target_on
  , target_start_time
  , target_end_time
  , expiration_at
)
VALUES
(
    :course_id
  , :application_id
  , :category
  , :option_no
  , :quantity
  , :target_on
  , :target_start_time
  , :target_end_time
  , (NOW() + INTERVAL 1 HOUR)
)
";

      $array = array(
          'course_id'         => $params['course_id']
        , 'application_id'    => $params['application_id']
        , 'category'          => $category
        , 'option_no'         => $params['option_no']
        , 'quantity'          => $params['quantity']
        , 'target_on'         => $params['target_on']
        , 'target_start_time' => $params['target_start_time']
        , 'target_end_time'   => $params['target_end_time']
      );

      //DB::enableQueryLog();
      $result = DB::insert($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（自由項目）登録
   * @param array $params - SQLパラメータ
   */
  public function regist_temp_etc($params)
  {

    $category = 'etc';

    DB::beginTransaction();

    try
    {
      $sql = "
INSERT INTO application_temp_details
(
    course_id
  , application_id
  , category
  , etc_no
  , quantity
  , expiration_at
)
VALUES
(
    :course_id
  , :application_id
  , :category
  , :etc_no
  , :quantity
  , (NOW() + INTERVAL 1 HOUR)
)
";

      $array = array(
          'course_id'         => $params['course_id']
        , 'application_id'    => $params['application_id']
        , 'category'          => $category
        , 'etc_no'            => $params['etc_no']
        , 'quantity'          => $params['quantity']
      );

      //DB::enableQueryLog();
      $result = DB::insert($sql, $array);
      //dd(DB::getQueryLog());

      DB::commit();
      //DB::rollBack();

      return array('status' => 'success', 'result' => $result);
    }
    catch (\PDOException $e)
    {
      DB::rollBack();
      session()->put("error", $e);
      return array('status' => 'failure', 'error' => json_encode($e, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
  }

  /**
   * 一時在庫（宿泊）を取得
   * $param string $couse_id - コースID
   */
  public function get_temp_inn($course_id)
  {
    $sql = "
SELECT
    inn_room_no
  , target_on
  , SUM(quantity) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = ?
AND
  category = 'inn'
AND
  expiration_at >= NOW()
GROUP BY
    inn_room_no
  , target_on
";
    $result = DB::Select($sql, [$course_id]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $i => $list)
      {
        foreach ($list as $key => $val)
        {
          $array[$i][$key] = $val;
        }
      }
    }

    return $array;
  }

  /**
   * 一時在庫（交通）を取得
   * $param string $couse_id - コースID
   */
  public function get_temp_traffic($course_id)
  {
    $sql = "
SELECT
    traffic_no
  , SUM(quantity) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = ?
AND
  category = 'traffic'
AND
  expiration_at >= NOW()
GROUP BY
  traffic_no
";
    $result = DB::Select($sql, [$course_id]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $i => $list)
      {
        foreach ($list as $key => $val)
        {
          $array[$i][$key] = $val;
        }
      }
    }

    return $array;
  }

  /**
   * 一時在庫（オプション）を取得
   * $param string $couse_id - コースID
   */
  public function get_temp_option($course_id)
  {
    $sql = "
SELECT
    option_no
  , target_on
  , target_start_time
  , target_end_time
  , SUM(quantity) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = ?
AND
  category = 'option'
AND
  expiration_at >= NOW()
GROUP BY
    option_no
  , target_on
  , target_start_time
  , target_end_time
";
    $result = DB::Select($sql, [$course_id]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $i => $list)
      {
        foreach ($list as $key => $val)
        {
          $array[$i][$key] = $val;
        }
      }
    }

    return $array;
  }

  /**
   * 一時在庫（自由項目）を取得
   * $param string $couse_id - コースID
   */
  public function get_temp_etc($course_id)
  {
    $sql = "
SELECT
    etc_no
  , SUM(quantity) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = ?
AND
  category = 'etc'
AND
  expiration_at >= NOW()
GROUP BY
    etc_no
";
    $result = DB::Select($sql, [$course_id]);
    $array = [];

    if (count($result) > 0 )
    {
      foreach ($result as $i => $list)
      {
        foreach ($list as $key => $val)
        {
          $array[$i][$key] = $val;
        }
      }
    }

    return $array;
  }

  /**
   * 一時在庫（宿泊）をチェック
   * $param array $params - パラメータ
   */
  public function check_temp_inn($params)
  {
    $course_id      = $params['course_id'];
    $application_id = $params['application_id'];
    $branch_no      = $params['branch_no'];
    $inn_no         = $params['inn_no'];
    $inn_room_no    = $params['inn_room_no'];
    $target_on      = $params['target_on'];
    $quantity       = (int) $params['quantity'];

    $details              = array();
    $my_reserves          = 0;
    $purchase             = 0;
    $stock                = 0;
    $temp_quantity        = 0;
    $temp_my_quantity     = 0;
    $remaining            = 0;      // 残数（公開数 - 全体予約数）
    $display_quantity     = 0;      // 表示残数（残数 + 自身予約数）
    $reservable_quantity  = 0;      // 申込可能残数（表示残数 - 一時在庫数）
    $reservable           = false;  // 一時在庫登録可否

    if (empty($application_id))
    {
      $seq_controller = new SequenceController;
      $application_id = $seq_controller->nextid('application_id');
      session()->put('applications.application_id', $application_id);
    }
    else
    {
      self::drop_temp_inn($application_id);
    }

    if (isset($application_id) && isset($branch_no))
    {
      $service    = new ApplicationService();
      $definition = $service->get_course_definition($course_id);
      $details    = $service->get_inn_room_counts($application_id, $branch_no);

      foreach ($details as $index => $detail)
      {
        if ($detail['inn_room_no'] == $inn_room_no && date('Ymd', strtotime($detail['target_on'])) == $target_on)
        {
          $my_reserves = $detail['count'];
          break;
        }
      }

      foreach ($definition['stocks']['inn']['inns'] as $index => $inns)
      {
        foreach ($inns['inn_rooms'] as $_index => $inn_rooms)
        {
          if ($inn_rooms['inn_room_no'] == $inn_room_no)
          {
            foreach ($inn_rooms['target'] as $targets)
            {
              $_target_on = date('Ymd', strtotime($targets['target_on']));
              if ($_target_on == $target_on)
              {
                $purchase   = (int) $targets['purchase'];
                $stock      = (int) $targets['stock'];
                $remaining  = (int) $definition['remaining']['inn_room'][$inn_no][$inn_room_no][$_target_on];

                break;
              }
            }
            break;
          }
        }
      }
    }

    // 表示残数 = 自身の予約数を加算
    $display_quantity = $remaining + $my_reserves;

    // 一時在庫（宿泊）取得
    $sql = "
SELECT
  IFNULL(SUM(quantity), 0) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = :course_id
AND
  category = 'inn'
AND
  inn_room_no = :inn_room_no
AND
  target_on = :target_on
AND
  expiration_at >= NOW()
";

    //DB::enableQueryLog();
    $result = DB::Select($sql, array(
        'course_id'   => $course_id
      , 'inn_room_no' => $inn_room_no
      , 'target_on'   => date('Y/m/d', strtotime($target_on))
    ));
    //dd(DB::getQueryLog());

    $temp_quantity = (int) $result[0]->temp_quantity;

    // 申込可能残数（一時在庫があれば減算）
    $reservable_quantity = $remaining - $temp_quantity;

    // 自身予約数から増えた場合のみ一時在庫の登録対象とする
    $temp_my_quantity = max(0, $quantity - $my_reserves);

    $reservable = ($reservable_quantity - $temp_my_quantity > -1);

    // 一時在庫登録可 && 0件超の増あり
    if ($reservable && $temp_my_quantity > 0)
    {
      // 一時在庫（登録）
      $array = array(
          'course_id'         => $course_id         // コースID
        , 'application_id'    => $application_id    // 申込ID
        , 'category'          => 'inn'              // カテゴリ
        , 'inn_no'            => $inn_no            // 宿泊施設番号
        , 'inn_room_no'       => $inn_room_no       // 宿泊部屋番号
        , 'target_on'         => date('Y/m/d', strtotime($target_on)) // 対象日
        , 'quantity'          => $temp_my_quantity  // 一時在庫登録数（一時在庫数 - 自身予約数）
      );
      self::regist_temp_inn($array);
    }

    return array(
        'course_id'           => $course_id             // コースID
      , 'inn_no'              => $inn_no                // 宿泊施設番号
      , 'inn_room_no'         => $inn_room_no           // 宿泊部屋番号
      , 'target_on'           => $target_on             // 対象日
      , 'purchase'            => $purchase              // 在庫数合計
      , 'stock'               => $stock                 // 公開数
      , 'my_reserves'         => $my_reserves           // 自身予約数
      , 'quantity'            => $quantity              // 選択数
      , 'temp_quantity'       => $temp_quantity         // 一時在庫数
      , 'temp_my_quantity'    => $temp_my_quantity      // 一時在庫登録数（一時在庫数 - 自身予約数）
      , 'reservable'          => $reservable            // 一時在庫登録可否
      , 'remaining'           => $remaining             // 残数（公開数 - 予約数）
      , 'display_quantity'    => $display_quantity      // 表示残数（残数 + 自身予約数）
      , 'reservable_quantity' => $reservable_quantity   // 申込可能残数（表示残数 - 一時在庫数）
    );
  }

  /**
   * 一時在庫（交通）をチェック
   * $param array $params - パラメータ
   */
  public function check_temp_traffic($params)
  {
    $course_id      = $params['course_id'];
    $application_id = $params['application_id'];
    $branch_no      = $params['branch_no'];
    $traffic_no     = $params['traffic_no'];
    $quantity       = (int) $params['quantity'];

    $details              = array();
    $my_reserves          = 0;
    $purchase             = 0;
    $stock                = 0;
    $temp_quantity        = 0;
    $temp_my_quantity     = 0;
    $remaining            = 0;      // 残数（公開数 - 全体予約数）
    $display_quantity     = 0;      // 表示残数（残数 + 自身予約数）
    $reservable_quantity  = 0;      // 申込可能残数（表示残数 - 一時在庫数）
    $reservable           = false;  // 一時在庫登録可否

    if (empty($application_id))
    {
      $seq_controller = new SequenceController;
      $application_id = $seq_controller->nextid('application_id');
      session()->put('applications.application_id', $application_id);
    }
    else
    {
      self::drop_temp_traffic($application_id);
    }

    if (isset($application_id) && isset($branch_no))
    {
      $service    = new ApplicationService();
      $definition = $service->get_course_definition($course_id);
      $details    = $service->get_traffic_counts($application_id, $branch_no);

      foreach ($details as $index => $detail)
      {
        if ($detail['traffic_no'] == $traffic_no)
        {
          $my_reserves = $detail['count'];
          break;
        }
      }

      foreach ($definition['stocks']['traffic']['traffics'] as $index => $traffics)
      {
        if ($traffics['traffic_no'] == $traffic_no)
        {
          $purchase   = (int) $traffics['purchase'];
          $stock      = (int) $traffics['stock'];
          $remaining  = (int) $definition['remaining']['traffic'][$traffic_no];
        }
      }
    }

    // 表示残数 = 自身の予約数を加算
    $display_quantity = $remaining + $my_reserves;

    // 一時在庫（交通）取得
    $sql = "
SELECT
  IFNULL(SUM(quantity), 0) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = :course_id
AND
  category = 'traffic'
AND
  traffic_no = :traffic_no
AND
  expiration_at >= NOW()
";

    //DB::enableQueryLog();
    $result = DB::Select($sql, array(
        'course_id'   => $course_id
      , 'traffic_no' => $traffic_no
    ));
    //dd(DB::getQueryLog());

    $temp_quantity = (int) $result[0]->temp_quantity;

    // 申込可能残数（一時在庫があれば減算）
    $reservable_quantity = $remaining - $temp_quantity;

    // 自身予約数から増えた場合のみ一時在庫の登録対象とする
    $temp_my_quantity = max(0, $quantity - $my_reserves);

    $reservable = ($reservable_quantity - $temp_my_quantity > -1);

    // 一時在庫登録可 && 0件超の増あり
    if ($reservable && $temp_my_quantity > 0)
    {
      // 一時在庫（登録）
      $array = array(
          'course_id'         => $course_id         // コースID
        , 'application_id'    => $application_id    // 申込ID
        , 'category'          => 'traffic'          // カテゴリ
        , 'traffic_no'        => $traffic_no        // 交通番号
        , 'quantity'          => $temp_my_quantity  // 一時在庫登録数（一時在庫数 - 自身予約数）
      );
      self::regist_temp_traffic($array);
    }

    return array(
        'course_id'           => $course_id             // コースID
      , 'traffic_no'          => $traffic_no            // 交通番号
      , 'purchase'            => $purchase              // 在庫数合計
      , 'stock'               => $stock                 // 公開数
      , 'my_reserves'         => $my_reserves           // 自身予約数
      , 'quantity'            => $quantity              // 選択数
      , 'temp_quantity'       => $temp_quantity         // 一時在庫数
      , 'temp_my_quantity'    => $temp_my_quantity      // 一時在庫登録数（一時在庫数 - 自身予約数）
      , 'reservable'          => $reservable            // 一時在庫登録可否
      , 'remaining'           => $remaining             // 残数（公開数 - 予約数）
      , 'display_quantity'    => $display_quantity      // 表示残数（残数 + 自身予約数）
      , 'reservable_quantity' => $reservable_quantity   // 申込可能残数（表示残数 - 一時在庫数）
    );
  }

  /**
   * 一時在庫（オプション）をチェック
   * $param array $params - パラメータ
   */
  public function check_temp_option($params)
  {
    $course_id          = $params['course_id'];
    $application_id     = $params['application_id'];
    $branch_no          = $params['branch_no'];
    $option_no          = $params['option_no'];
    $target_on          = $params['target_on'];
    $target_start_time  = $params['target_start_time'];
    $target_end_time    = $params['target_end_time'];
    $quantity           = (int) $params['quantity'];

    $details              = array();
    $my_reserves          = 0;
    $purchase             = 0;
    $stock                = 0;
    $temp_quantity        = 0;
    $temp_my_quantity     = 0;
    $remaining            = 0;      // 残数（公開数 - 全体予約数）
    $display_quantity     = 0;      // 表示残数（残数 + 自身予約数）
    $reservable_quantity  = 0;      // 申込可能残数（表示残数 - 一時在庫数）
    $reservable           = false;  // 一時在庫登録可否

    if (empty($application_id))
    {
      $seq_controller = new SequenceController;
      $application_id = $seq_controller->nextid('application_id');
      session()->put('applications.application_id', $application_id);
    }
    else
    {
      self::drop_temp_option($application_id);
    }

    if (isset($application_id) && isset($branch_no))
    {
      $service    = new ApplicationService();
      $definition = $service->get_course_definition($course_id);
      $details    = $service->get_option_counts($application_id, $branch_no);

      foreach ($details as $index => $detail)
      {
        if (
          $detail['option_no'] == $option_no
          &&
          date('Ymd', strtotime($detail['target_on'])) == $target_on
          &&
          date('Hi', strtotime($detail['target_start_time'])) == $target_start_time
          &&
          date('Hi', strtotime($detail['target_end_time'])) == $target_end_time
        )
        {
          $my_reserves = $detail['count'];
          break;
        }
      }

      foreach ($definition['stocks']['option']['options'] as $index => $options)
      {
        if ($options['option_no'] == $option_no)
        {
          foreach ($options['target'] as $targets)
          {
            $_target_on         = date('Ymd', strtotime($targets['target_on']));
            $_target_start_time = date('Hi', strtotime($targets['target_start_time']));
            $_target_end_time   = date('Hi', strtotime($targets['target_end_time']));

            if (
              $_target_on == $target_on
              &&
              $_target_start_time == $target_start_time
              &&
              $_target_end_time == $target_end_time
            )
            {
              $purchase   = (int) $targets['purchase'];
              $stock      = (int) $targets['stock'];
              $remaining  = (int) $definition['remaining']['option'][$option_no][$_target_on][$_target_start_time][$_target_end_time];

              break;
            }
          }
          break;
        }
      }
    }

    // 表示残数 = 自身の予約数を加算
    $display_quantity = $remaining + $my_reserves;

    // 一時在庫（オプション）取得
    $sql = "
SELECT
  IFNULL(SUM(quantity), 0) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = :course_id
AND
  category = 'option'
AND
  option_no = :option_no
AND
  target_on = :target_on
AND
  target_start_time = :target_start_time
AND
  target_end_time = :target_end_time
AND
  expiration_at >= NOW()
";

    //DB::enableQueryLog();
    $result = DB::Select($sql, array(
        'course_id'         => $course_id
      , 'option_no'         => $option_no
      , 'target_on'         => date('Y/m/d', strtotime($target_on))
      , 'target_start_time' => date('H:i', strtotime($target_start_time))
      , 'target_end_time'   => date('H:i', strtotime($target_end_time))
    ));
    //dd(DB::getQueryLog());

    $temp_quantity = (int) $result[0]->temp_quantity;

    // 申込可能残数（一時在庫があれば減算）
    $reservable_quantity = $remaining - $temp_quantity;

    // 自身予約数から増えた場合のみ一時在庫の登録対象とする
    $temp_my_quantity = max(0, $quantity - $my_reserves);

    $reservable = ($reservable_quantity - $temp_my_quantity > -1);

    // 一時在庫登録可 && 0件超の増あり
    if ($reservable && $temp_my_quantity > 0)
    {
      // 一時在庫（登録）
      $array = array(
          'course_id'         => $course_id         // コースID
        , 'application_id'    => $application_id    // 申込ID
        , 'category'          => 'option'           // カテゴリ
        , 'option_no'         => $option_no         // オプション番号
        , 'target_on'         => date('Y/m/d', strtotime($target_on))       // 対象日
        , 'target_start_time' => date('H:i', strtotime($target_start_time)) // 対象開始時間
        , 'target_end_time'   => date('H:i', strtotime($target_end_time))   // 対象終了時間
        , 'quantity'          => $temp_my_quantity  // 一時在庫登録数（一時在庫数 - 自身予約数）
      );
      self::regist_temp_option($array);
    }

    return array(
        'course_id'           => $course_id           // コースID
      , 'option_no'           => $option_no           // オプション番号
      , 'target_on'           => $target_on           // 対象日
      , 'target_start_time'   => $target_start_time   // 対象開始時間
      , 'target_end_time'     => $target_end_time     // 対象終了時間
      , 'purchase'            => $purchase            // 在庫数合計
      , 'stock'               => $stock               // 公開数
      , 'my_reserves'         => $my_reserves         // 自身予約数
      , 'quantity'            => $quantity            // 選択数
      , 'temp_quantity'       => $temp_quantity       // 一時在庫数
      , 'temp_my_quantity'    => $temp_my_quantity    // 一時在庫登録数（一時在庫数 - 自身予約数）
      , 'reservable'          => $reservable          // 一時在庫登録可否
      , 'remaining'           => $remaining           // 残数（公開数 - 予約数）
      , 'display_quantity'    => $display_quantity    // 表示残数（残数 + 自身予約数）
      , 'reservable_quantity' => $reservable_quantity // 申込可能残数（表示残数 - 一時在庫数）
    );
  }

  /**
   * 一時在庫（etc）をチェック
   * $param array $params - パラメータ
   */
  public function check_temp_etc($params)
  {
    $course_id      = $params['course_id'];
    $application_id = $params['application_id'];
    $branch_no      = $params['branch_no'];
    $etc_no         = $params['etc_no'];
    $quantity       = (int) $params['quantity'];

    $details              = array();
    $my_reserves          = 0;
    $purchase             = 0;
    $stock                = 0;
    $temp_quantity        = 0;
    $temp_my_quantity     = 0;
    $remaining            = 0;      // 残数（公開数 - 全体予約数）
    $display_quantity     = 0;      // 表示残数（残数 + 自身予約数）
    $reservable_quantity  = 0;      // 申込可能残数（表示残数 - 一時在庫数）
    $reservable           = false;  // 一時在庫登録可否

    if (empty($application_id))
    {
      $seq_controller = new SequenceController;
      $application_id = $seq_controller->nextid('application_id');
      session()->put('applications.application_id', $application_id);
    }
    else
    {
      self::drop_temp_etc($application_id);
    }

    if (isset($application_id) && isset($branch_no))
    {
      $service    = new ApplicationService();
      $definition = $service->get_course_definition($course_id);
      $details    = $service->get_etc_counts($application_id, $branch_no);

      foreach ($details as $index => $detail)
      {
        if ($detail['etc_no'] == $etc_no)
        {
          $my_reserves = $detail['count'];
          break;
        }
      }

      foreach ($definition['stocks']['etc']['etc'] as $index => $etc)
      {
        if ($etc['etc_no'] == $etc_no)
        {
          $purchase   = (int) $etc['purchase'];
          $stock      = (int) $etc['stock'];
          $remaining  = (int) $definition['remaining']['etc'][$etc_no];
        }
      }
    }

    // 表示残数 = 自身の予約数を加算
    $display_quantity = $remaining + $my_reserves;

    // 一時在庫（etc）取得
    $sql = "
SELECT
  IFNULL(SUM(quantity), 0) AS temp_quantity
FROM
  application_temp_details
WHERE
  course_id = :course_id
AND
  category = 'etc'
AND
  etc_no = :etc_no
AND
  expiration_at >= NOW()
";

    //DB::enableQueryLog();
    $result = DB::Select($sql, array(
        'course_id'   => $course_id
      , 'etc_no' => $etc_no
    ));
    //dd(DB::getQueryLog());

    $temp_quantity = (int) $result[0]->temp_quantity;

    // 申込可能残数（一時在庫があれば減算）
    $reservable_quantity = $remaining - $temp_quantity;

    // 自身予約数から増えた場合のみ一時在庫の登録対象とする
    $temp_my_quantity = max(0, $quantity - $my_reserves);

    $reservable = ($reservable_quantity - $temp_my_quantity > -1);

    // 一時在庫登録可 && 0件超の増あり
    if ($reservable && $temp_my_quantity > 0)
    {
      // 一時在庫（登録）
      $array = array(
          'course_id'         => $course_id         // コースID
        , 'application_id'    => $application_id    // 申込ID
        , 'category'          => 'etc'              // カテゴリ
        , 'etc_no'            => $etc_no            // etc番号
        , 'quantity'          => $temp_my_quantity  // 一時在庫登録数（一時在庫数 - 自身予約数）
      );
      self::regist_temp_etc($array);
    }

    return array(
        'course_id'           => $course_id             // コースID
      , 'etc_no'              => $etc_no        // etc番号
      , 'purchase'            => $purchase              // 在庫数合計
      , 'stock'               => $stock                 // 公開数
      , 'my_reserves'         => $my_reserves           // 自身予約数
      , 'quantity'            => $quantity              // 選択数
      , 'temp_quantity'       => $temp_quantity         // 一時在庫数
      , 'temp_my_quantity'    => $temp_my_quantity      // 一時在庫登録数（一時在庫数 - 自身予約数）
      , 'reservable'          => $reservable            // 一時在庫登録可否
      , 'remaining'           => $remaining             // 残数（公開数 - 予約数）
      , 'display_quantity'    => $display_quantity      // 表示残数（残数 + 自身予約数）
      , 'reservable_quantity' => $reservable_quantity   // 申込可能残数（表示残数 - 一時在庫数）
    );
  }

}
