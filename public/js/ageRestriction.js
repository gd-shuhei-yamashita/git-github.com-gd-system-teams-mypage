/**
AgeRestriction.js v1
営業日制御ライブラリ

改修元↓
Copyright (c) 2020 YamamotoKoshiro
Released under the MIT license
https://github.com/mynavi-code/ageRestriction/blob/master/LICENSE

使い方
1.このライブラリをダウンロードし、該当ページにてインポートしてください（例）<script src="ageRestriction.js"></script>
2.インスタンスを生成してください（例）new AgeRestriction('year', 'month', 'day', 10);
第1引数：年select要素のid属性
第2引数：月select要素のid属性
第3引数：日select要素のid属性
第4引数：スキップしたい営業日数
*/

var holidayList = [
    '2022/1/1',
    '2022/1/10',
    '2022/2/11',
    '2022/2/23',
    '2022/3/21',
    '2022/4/29',
    '2022/5/3',
    '2022/5/4',
    '2022/5/5',
    '2022/7/18',
    '2022/8/11',
    '2022/9/19',
    '2022/9/23',
    '2022/10/10',
    '2022/11/3',
    '2022/11/23',
    '2023/1/1',
    '2023/1/2',
    '2023/1/9',
    '2023/2/11',
    '2023/2/23',
    '2023/3/21',
    '2023/4/29',
    '2023/5/3',
    '2023/5/4',
    '2023/5/5',
    '2023/7/17',
    '2023/8/11',
    '2023/9/18',
    '2023/9/23',
    '2023/10/9',
    '2023/11/3',
    '2023/11/23',
    '2024/1/1',
    '2024/1/8',
    '2024/2/11',
    '2024/2/12',
    '2024/2/23',
    '2024/3/20',
    '2024/4/29',
    '2024/5/3',
    '2024/5/4',
    '2024/5/5',
    '2024/5/6',
    '2024/7/15',
    '2024/8/11',
    '2024/8/12',
    '2024/9/16',
    '2024/9/22',
    '2024/9/23',
    '2024/10/14',
    '2024/11/3',
    '2024/11/4',
    '2024/11/23',
    '2025/1/1',
    '2025/1/13',
    '2025/2/11',
    '2025/2/23',
    '2025/2/24',
    '2025/3/20',
    '2025/4/29',
    '2025/5/3',
    '2025/5/4',
    '2025/5/5',
    '2025/5/6',
    '2025/7/21',
    '2025/8/11',
    '2025/9/15',
    '2025/9/23',
    '2025/10/13',
    '2025/11/3',
    '2025/11/23',
    '2025/11/24',
    '2026/1/1',
    '2026/1/12',
    '2026/2/11',
    '2026/2/23',
    '2026/3/20',
    '2026/4/29',
    '2026/5/3',
    '2026/5/4',
    '2026/5/5',
    '2026/5/6',
    '2026/7/20',
    '2026/8/11',
    '2026/9/21',
    '2026/9/22',
    '2026/9/23',
    '2026/10/12',
    '2026/11/3',
    '2026/11/23',
    '2027/1/1',
    '2027/1/11',
    '2027/2/11',
    '2027/2/23',
    '2027/3/21',
    '2027/3/22',
    '2027/4/29',
    '2027/5/3',
    '2027/5/4',
    '2027/5/5',
    '2027/7/19',
    '2027/8/11',
    '2027/9/20',
    '2027/9/23',
    '2027/10/11',
    '2027/11/3',
    '2027/11/23',
    '2028/1/1',
    '2028/1/10',
    '2028/2/11',
    '2028/2/23',
    '2028/3/20',
    '2028/4/29',
    '2028/5/3',
    '2028/5/4',
    '2028/5/5',
    '2028/7/17',
    '2028/8/11',
    '2028/9/18',
    '2028/9/22',
    '2028/10/9',
    '2028/11/3',
    '2028/11/23',
    '2029/1/1',
    '2029/1/8',
    '2029/2/11',
    '2029/2/12',
    '2029/2/23',
    '2029/3/20',
    '2029/4/29',
    '2029/4/30',
    '2029/5/3',
    '2029/5/4',
    '2029/5/5',
    '2029/7/16',
    '2029/8/11',
    '2029/9/17',
    '2029/9/23',
    '2029/9/24',
    '2029/10/8',
    '2029/11/3',
    '2029/11/23',
    '2030/1/1',
    '2030/1/14',
    '2030/2/11',
    '2030/2/23',
    '2030/3/20',
    '2030/4/29',
    '2030/5/3',
    '2030/5/4',
    '2030/5/5',
    '2030/5/6',
    '2030/7/15',
    '2030/8/11',
    '2030/8/12',
    '2030/9/16',
    '2030/9/23',
    '2030/10/14',
    '2030/11/3',
    '2030/11/4',
    '2030/11/23',
    '2031/1/1',
    '2031/1/13',
    '2031/2/11',
    '2031/2/23',
    '2031/2/24',
    '2031/3/21',
    '2031/4/29',
    '2031/5/3',
    '2031/5/4',
    '2031/5/5',
    '2031/5/6',
    '2031/7/21',
    '2031/8/11',
    '2031/9/15',
    '2031/9/23',
    '2031/10/13',
    '2031/11/3',
    '2031/11/23',
    '2031/11/24',
    '2032/1/1',
    '2032/1/12',
    '2032/2/11',
    '2032/2/23',
    '2032/3/20',
    '2032/4/29',
    '2032/5/3',
    '2032/5/4',
    '2032/5/5',
    '2032/7/19',
    '2032/8/11',
    '2032/9/20',
    '2032/9/21',
    '2032/9/22',
    '2032/10/11',
    '2032/11/3',
    '2032/11/23',
    '2033/1/1',
    '2033/1/10',
    '2033/2/11',
    '2033/2/23',
    '2033/3/20',
    '2033/3/21',
    '2033/4/29',
    '2033/5/3',
    '2033/5/4',
    '2033/5/5',
    '2033/7/18',
    '2033/8/11',
    '2033/9/19',
    '2033/9/23',
    '2033/10/10',
    '2033/11/3',
    '2033/11/23',
    '2034/1/1',
    '2034/1/2',
    '2034/1/9',
    '2034/2/11',
    '2034/2/23',
    '2034/3/20',
    '2034/4/29',
    '2034/5/3',
    '2034/5/4',
    '2034/5/5',
    '2034/7/17',
    '2034/8/11',
    '2034/9/18',
    '2034/9/23',
    '2034/10/9',
    '2034/11/3',
    '2034/11/23',
    '2035/1/1',
    '2035/1/8',
    '2035/2/11',
    '2035/2/12',
    '2035/2/23',
    '2035/3/21',
    '2035/4/29',
    '2035/4/30',
    '2035/5/3',
    '2035/5/4',
    '2035/5/5',
    '2035/7/16',
    '2035/8/11',
    '2035/9/17',
    '2035/9/23',
    '2035/9/24',
    '2035/10/8',
    '2035/11/3',
    '2035/11/23',
    '2036/1/1',
    '2036/1/14',
    '2036/2/11',
    '2036/2/23',
    '2036/3/20',
    '2036/4/29',
    '2036/5/3',
    '2036/5/4',
    '2036/5/5',
    '2036/5/6',
    '2036/7/21',
    '2036/8/11',
    '2036/9/15',
    '2036/9/22',
    '2036/10/13',
    '2036/11/3',
    '2036/11/23',
    '2036/11/24',
];

class AgeRestriction {

    constructor(yearId, monthId, dayId, skipDays) {
        this.nowDate = new Date();
        this.nowYear = this.nowDate.getFullYear();
        this.nowMonth = this.nowDate.getMonth() + 1;
        this.nowDay = this.nowDate.getDate();
        this.yearObj = document.getElementById(yearId);
        this.monthObj = document.getElementById(monthId);
        this.dayObj = document.getElementById(dayId);
        this.year;
        this.month;
        this.firstMonth;
        this.lastMonth;
        this.firstDay;
        this.lastDay;
        this.holidayList = holidayList;
        this.skipDays = skipDays;
        this.afterDay = new Date();
        this.afterDay.setDate(this.nowDay + this.calcBusinessDay());

        this.init(this);
    }

    calcBusinessDay() {
        var checkDay = new Date();
        var count = 0;
        var businessDay = 0;
        while (count < this.skipDays) {
            businessDay++;
            checkDay.setDate(checkDay.getDate() + 1);
            if (checkDay.getDay() != 0 && checkDay.getDay() != 6) {
                var checkDayYear = checkDay.getFullYear();
                var checkDayMonth = checkDay.getMonth() + 1;
                var checkDayDay = checkDay.getDate();
                if (!this.holidayList.includes(checkDayYear + '/' + checkDayMonth + '/' + checkDayDay)) {
                    count++;
                }
            }
        }
        return businessDay;
    }

    // 年 セット
    setYear() {
        this.year = this.yearObj.value;
    }

    // 月 セット
    setMonth() {
        this.month = this.monthObj.value;
    }

    // 年描画
    outputYear() {
        let option = '<option value="">選択</option> ';
        var firstYear = this.afterDay.getFullYear();
        for (let i = firstYear; i <= firstYear + 10; i++) {
            option += '<option value="' + i + '">' + i + '</option>\n';
        }
        this.yearObj.innerHTML = option;
    }

    // ループ月　セット
    setFirstLastMonth() {
        this.setYear();
        this.firstMonth = 1;
        this.endMonth = 12;
        if (this.afterDay.getFullYear() == this.year) {
            this.firstMonth = this.afterDay.getMonth() + 1;
        }
    }

    // 月描画
    outputMonth() {
        this.setFirstLastMonth();
        let option = '<option value="">選択</option> ';
        for (let i = this.firstMonth; i <= this.endMonth; i++) {
            option += '<option value="' + i + '">' + i + '</option>\n';
        }
        this.monthObj.innerHTML = option;
    }

    // ループ日付 セット
    setFirstLastDay() {
        this.setYear();
        this.setMonth();
        let monthLastday = ['', 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (this.leapYearCheck(this.year)) {
            monthLastday[2] = 29;
        }
        this.lastDay = monthLastday[this.month];
        this.firstDay = 1;
        if (this.afterDay.getFullYear() == this.year && (this.afterDay.getMonth() + 1) == this.month) {
            if (this.afterDay.getDate() <= this.lastDay) {
                this.firstDay = this.afterDay.getDate();
            }
        }
    }

    // 日付描画 
    outputDay() {
        this.setFirstLastDay();
        let option = '<option value="">選択</option> ';
        for (let i = this.firstDay; i <= this.lastDay; i++) {
            option += '<option value="' + i + '">' + i + '</option>\n';
        }
        this.dayObj.innerHTML = option;
    }

    // うるう年計算
    leapYearCheck(year) {
        if ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0) {
            return true
        }
        return false;
    }

    // イベントセット
    init(obj) {
        obj.outputYear();
        obj.outputMonth();
        obj.outputDay();
        obj.yearObj.addEventListener('change', function () {
            obj.outputMonth();
            obj.outputDay();
        });
        obj.monthObj.addEventListener('change', function () {
            obj.outputDay();
        });
    }
}
