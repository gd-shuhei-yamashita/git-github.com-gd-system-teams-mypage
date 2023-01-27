<?php

use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brand')->insert([
            'id' => "1",
            'code' => "BD00000001",
            'name' => "テスト でんき",
            'name_printed' => "テスト でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2021/02/important_H.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "2",
            'code' => "BD00000002",
            'name' => "B20_モダでん（フリー）",
            'name_printed' => "モダでん",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B20_06-1.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "3",
            'code' => "BD00000003",
            'name' => "B02_過去獲得再送用①（ファミリー、ハッピー、生活安心、ベーシック）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B02_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "4",
            'code' => "BD00000004",
            'name' => "B03_過去獲得再送用②（Webコン、エンタメ、低圧電力基本、サポート）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B03_08.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "5",
            'code' => "BD00000005",
            'name' => "B04_ONEでんき（フリー、Mプラン）",
            'name_printed' => "ONEでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B04_08-2.pdf",
            'contact_url' => "https://onedenki.jp/",
            'phone' => "0570-070-336",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "6",
            'code' => "BD00000006",
            'name' => "B19_ドアーズでんき（フリー、スタート、S）",
            'name_printed' => "ドアーズでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B19_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "7",
            'code' => "BD00000007",
            'name' => "B05_スマエネでんき（シンプル、スタート、M、S）",
            'name_printed' => "スマエネでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B05_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "8",
            'code' => "BD00000008",
            'name' => "B21_セレクトでんき（シンプル、スタート、M、S）",
            'name_printed' => "セレクトでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B21_07.pdf",
            'contact_url' => "https://selectdenki.com/",
            'phone' => "0570-200-609",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "9",
            'code' => "BD00000009",
            'name' => "B22_くらしエナジー（シンプル、スタート、スマートシンプル、M、S）",
            'name_printed' => "くらしエナジー",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B22_07.pdf",
            'contact_url' => "https://kurashienergy.jp/",
            'phone' => "0570-060-228",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "10",
            'code' => "BD00000010",
            'name' => "B23_島根でんき（シンプル、スタート、M、S）",
            'name_printed' => "島根でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B23_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "11",
            'code' => "BD00000011",
            'name' => "B24_ONLYでんき（シンプル、スタート、M、S）",
            'name_printed' => "ONLYでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B24_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "12",
            'code' => "BD00000012",
            'name' => "B25_アレコでんき（シンプル、スタート）",
            'name_printed' => "アレコでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B25_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "13",
            'code' => "BD00000013",
            'name' => "B26_くらしのでんき（シンプル、スタート、M、S）",
            'name_printed' => "くらしのでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B26_07.pdf",
            'contact_url' => "https://kurashinodenki.com/",
            'phone' => "0570-550-920",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "14",
            'code' => "BD00000014",
            'name' => "B27_シティネットでんき（シンプル、スタート、フリー）",
            'name_printed' => "シティネットでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B27_08.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "15",
            'code' => "BD00000015",
            'name' => "B06_ライフでんき（シンプル、スタート、デジコン）",
            'name_printed' => "ライフでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B06_06.pdf",
            'contact_url' => "https://lifedenki.com/",
            'phone' => "0570-550-019",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "16",
            'code' => "BD00000016",
            'name' => "B28_エコ得でんき（シンプル、スタート、デジコン）",
            'name_printed' => "エコ得でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B28_06.pdf",
            'contact_url' => "https://ecotokudenki.com/",
            'phone' => "0570-783-307",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "17",
            'code' => "BD00000017",
            'name' => "B11_NEXTでんき（シンプル、スタート、デジコン、ウェルカム）",
            'name_printed' => "NEXTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B11_07.pdf",
            'contact_url' => "https://nextdenki.jp/",
            'phone' => "0570-011-531",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "18",
            'code' => "BD00000018",
            'name' => "B12_ABEMAでんき（プレミアム）",
            'name_printed' => "ABEMAでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B12_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "19",
            'code' => "BD00000019",
            'name' => "B13_どうぶつでんき（スタート、ペット賠償、ペットハート、ペットハートプレミアム）",
            'name_printed' => "どうぶつでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B13_07.pdf",
            'contact_url' => "https://doubutsudenki.com/",
            'phone' => "0570-010-618",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "20",
            'code' => "BD00000020",
            'name' => "B14_賃貸でんき（スタート、フリー、低圧電力、従量電灯、S）",
            'name_printed' => "賃貸でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B14_07-1.pdf",
            'contact_url' => "https://chintaidenki.jp/",
            'phone' => "0570-550-260",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "21",
            'code' => "BD00000021",
            'name' => "B15_エンタメでんき（オンデマンド）",
            'name_printed' => "エンタメでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/03/b9e3dca42c3894053064e64941b3816d.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "1",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "22",
            'code' => "BD00000022",
            'name' => "B17_シンプルでんき（シンプル、従量電灯、低圧電力）",
            'name_printed' => "シンプルでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B17_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "23",
            'code' => "BD00000023",
            'name' => "B18_UTでんき（社宅）",
            'name_printed' => "UTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B18_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "24",
            'code' => "BD00000024",
            'name' => "B01_法人用（従量電灯、低圧電力、空室、フリー）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B01_06-1.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "25",
            'code' => "BD00000025",
            'name' => "B03_過去獲得再送用②（Webコン、エンタメ、低圧電力基本、サポート）",
            'name_printed' => "セレクトでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B03_08.pdf",
            'contact_url' => "https://selectdenki.com/",
            'phone' => "0570-200-609",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "26",
            'code' => "BD00000026",
            'name' => "B03_過去獲得再送用②（Webコン、エンタメ、低圧電力基本、サポート）",
            'name_printed' => "ライフでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B03_08.pdf",
            'contact_url' => "https://lifedenki.com/",
            'phone' => "0570-550-019",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "27",
            'code' => "BD00000027",
            'name' => "B03_過去獲得再送用②（Webコン、エンタメ、低圧電力基本、サポート）",
            'name_printed' => "エコ得でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B03_08.pdf",
            'contact_url' => "https://ecotokudenki.com/",
            'phone' => "0570-783-307",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "28",
            'code' => "BD00000028",
            'name' => "B02_過去獲得再送用①（ファミリー、ハッピー、生活安心、ベーシック）",
            'name_printed' => "NEXTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B03_07.pdf",
            'contact_url' => "https://nextdenki.jp/",
            'phone' => "0570-011-531",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "29",
            'code' => "BD00000029",
            'name' => "B03_過去獲得再送用②（Webコン、エンタメ、低圧電力基本、サポート）",
            'name_printed' => "NEXTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B03_08.pdf",
            'contact_url' => "https://nextdenki.jp/",
            'phone' => "0570-011-531",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "30",
            'code' => "BD00000030",
            'name' => "B36_汎用（ライフでんきスマートフリー）",
            'name_printed' => "ライフでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B36_06.pdf",
            'contact_url' => "https://lifedenki.com/",
            'phone' => "0570-550-019",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "31",
            'code' => "BD00000031",
            'name' => "B29_NEXTでんき（フリー、スマートシンプル、M、S）",
            'name_printed' => "NEXTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B29_08.pdf",
            'contact_url' => "https://nextdenki.jp/",
            'phone' => "0570-011-531",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "32",
            'code' => "BD00000032",
            'name' => "B39_ライフでんき（スマートシンプル、M、S）",
            'name_printed' => "ライフでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B39_05.pdf",
            'contact_url' => "https://lifedenki.com/",
            'phone' => "0570-550-019",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "33",
            'code' => "BD00000033",
            'name' => "B38_エコ得でんき（スマートシンプル、M、S）",
            'name_printed' => "エコ得でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B38_05.pdf",
            'contact_url' => "https://ecotokudenki.com/",
            'phone' => "0570-783-307",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "34",
            'code' => "BD00000034",
            'name' => "B30_すまいのでんき（シンプル、スタート）",
            'name_printed' => "すまいのでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/03/5fe1c3c7cd26161feec39f55f697ac9b.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "1",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "35",
            'code' => "BD00000035",
            'name' => "B31_メルディアでんき（フリー、M）",
            'name_printed' => "メルディアでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B31_07.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "36",
            'code' => "BD00000036",
            'name' => "B33_見直し本舗でんき（フリープラン）",
            'name_printed' => "見直し本舗でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B33_05-1.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "37",
            'code' => "BD00000037",
            'name' => "B35_汎用（シンプル&スタート）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/03/B35_04.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "1",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "38",
            'code' => "BD00000038",
            'name' => "B36_汎用（スマートフリー）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B36_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "39",
            'code' => "BD00000039",
            'name' => "B37_汎用（スマートシンプル）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B37_04.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "40",
            'code' => "BD00000040",
            'name' => "B40_セレクトでんき（スマートシンプル）",
            'name_printed' => "セレクトでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B40_03.pdf",
            'contact_url' => "https://selectdenki.com/",
            'phone' => "0570-200-609",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "41",
            'code' => "BD00000041",
            'name' => "B16_汎用（シンプルのみ）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B16_06.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "42",
            'code' => "BD00000042",
            'name' => "B41_どうぶつでんき（S、M、ペットシェルジュ、ペットシェルジュプレミアム）",
            'name_printed' => "どうぶつでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/08/B41_06.pdf",
            'contact_url' => "https://doubutsudenki.com/",
            'phone' => "0570-010-618",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "43",
            'code' => "BD00000043",
            'name' => "B02_過去獲得再送用①（ファミリー、ハッピー、生活安心、ベーシック）",
            'name_printed' => "エコ得でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B02_06.pdf",
            'contact_url' => "https://ecotokudenki.com/",
            'phone' => "0570-783-307",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "44",
            'code' => "BD00000044",
            'name' => "B42_賃貸でんき（スマートシンプル、M）",
            'name_printed' => "賃貸でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B42_04.pdf",
            'contact_url' => "https://chintaidenki.jp/",
            'phone' => "0570-550-260",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "45",
            'code' => "BD00000045",
            'name' => "B44_レオパレスでんき（基本）",
            'name_printed' => "レオパレスでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B44_02-2.pdf",
            'contact_url' => "",
            'phone' => "0570-010-250",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "46",
            'code' => "BD00000046",
            'name' => "B45_汎用（オール電化）",
            'name_printed' => "㈱グランデータ",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B45_02.pdf",
            'contact_url' => "",
            'phone' => "",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "47",
            'code' => "BD00000047",
            'name' => "B46_エコ得でんき（オール電化）",
            'name_printed' => "エコ得でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B46_02.pdf",
            'contact_url' => "https://ecotokudenki.com/",
            'phone' => "0570-783-307",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "48",
            'code' => "BD00000048",
            'name' => "B47_ライフでんき（オール電化）",
            'name_printed' => "ライフでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B47_02.pdf",
            'contact_url' => "https://lifedenki.com/",
            'phone' => "0570-550-019",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "49",
            'code' => "BD00000049",
            'name' => "B48_NEXTでんき（オール電化）",
            'name_printed' => "NEXTでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B48_02.pdf",
            'contact_url' => "https://nextdenki.jp/",
            'phone' => "0570-011-531",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "50",
            'code' => "BD00000050",
            'name' => "B49_くらしエナジー（オール電化）",
            'name_printed' => "くらしエナジー",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B49_02.pdf",
            'contact_url' => "https://kurashienergy.jp/",
            'phone' => "0570-060-228",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "51",
            'code' => "BD00000051",
            'name' => "B50_セレクトでんき（オール電化）",
            'name_printed' => "セレクトでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B50_02.pdf",
            'contact_url' => "https://selectdenki.com/",
            'phone' => "0570-200-609",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "52",
            'code' => "BD00000052",
            'name' => "B51_賃貸でんき（オール電化）",
            'name_printed' => "賃貸でんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B51_02.pdf",
            'contact_url' => "https://chintaidenki.jp/",
            'phone' => "0570-550-260",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "53",
            'code' => "BD00000053",
            'name' => "B52_ONEでんき（スタンダード）",
            'name_printed' => "ONEでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B52_02.pdf",
            'contact_url' => "https://onedenki.jp/",
            'phone' => "0570-070-336",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "54",
            'code' => "BD00000054",
            'name' => "B53_くらしエナジー（基本）",
            'name_printed' => "くらしエナジー",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/06/B53-01-1.pdf",
            'contact_url' => "https://kurashienergy.jp/",
            'phone' => "0570-060-228",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

        DB::table('brand')->insert([
            'id' => "55",
            'code' => "BD00000055",
            'name' => "B54_ONEでんき（特別）",
            'name_printed' => "ONEでんき",
            'explanation_url' => "https://grandata-service.jp/wp/wp-content/uploads/2022/07/B54_01.pdf",
            'contact_url' => "https://onedenki.jp/",
            'phone' => "0570-070-336",
            'status' => "0",
            'created_user_id' => "seeder",
        ]);

    }
}
