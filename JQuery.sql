SELECT 
	distinct A.seikyu_cd||':'||A.c_bp_group_id AS ID, 
	'Y' as select, 
	A.seikyu_cd AS seikyu_cd, 
	B.nameryaku AS seikyusaki_name, 
	substr(cast(A.nengetu AS character varying), 1, 4)||'年'||substr(cast(A.nengetu AS character varying), 5, 6)||'月' AS nengetu, 
	ltrim(C.name)AS sime_kbn, 
	CASE count(D.ruikei_seikyu) 
		WHEN 0 THEN '0' 
		ELSE to_char(D.ruikei_seikyu, 'FM999,999,999') 
		END AS zenkai_seikyu_zandaka, 
	CASE count(A.zenkai_nyukingak) 
		WHEN 0 THEN '0' 
		ELSE to_char(A.zenkai_nyukingak, 'FM999,999,999') 
		END AS zenkai_nyukingak, 
	CASE count(A.zenkai_zandaka) 
		WHEN 0 THEN '0' 
		ELSE to_char(A.zenkai_zandaka, 'FM999,999,999') 
		END AS kurikosi_zandaka, 
	CASE count(A.seikyu_kingak) 
		WHEN 0 THEN '0' 
		ELSE to_char((A.seikyu_kingak), 'FM999,999,999') 
		END AS zeinuki_kingak, 
	CASE count(A.syohizei) 
		WHEN 0 THEN '0' 
		ELSE to_char(A.syohizei, 'FM999,999,999') 
		END AS syohizei, 
	CASE count(A.seikyu_kingak) 
		WHEN 0 THEN '0' 
		ELSE to_char(A.seikyu_kingak + A.syohizei, 'FM999,999,999') 
		END AS seikyu_kingak, 
	CASE count(A.ruikei_seikyu) 
		WHEN 0 THEN '0' 
		ELSE to_char(A.ruikei_seikyu, 'FM999,999,999') 
		END AS kingak, to_char(A.kaisyu_yoteibi, 'yyyy/mm/dd') AS kaisyu_yoteibi
FROM euc_seikyu AS A 
LEFT OUTER JOIN c_bp_group AS B 
	ON (B.c_bp_group_id=A.c_bp_group_id AND B.IsActive='Y') 
LEFT OUTER JOIN euc_kbn AS C 
	ON (C.kbn_cd=? AND C.cd=B.sime_kbn) 
LEFT OUTER JOIN euc_seikyu AS D 
	ON D.seikyu_cd=(
		SELECT MAX(seikyu_cd) 
		FROM euc_seikyu AS E 
		WHERE sakujo_kbn = '1' and A.seikyu_cd>E.seikyu_cd AND A.c_bp_group_id=E.c_bp_group_id )  
GROUP BY 
		A.seikyu_cd,
		A.c_bp_group_id,
		B.nameryaku,
		A.nengetu,
		C.name,
		D.zenkai_nyukingak,
		D.ruikei_seikyu,
		A.zenkai_nyukingak,
		A.ruikei_seikyu,
		A.syohizei,
		A.zenkai_zandaka,
		A.seikyu_kingak,
		A.kaisyu_yoteibi
ORDER BY A.seikyu_cd,seikyusaki_name






SELECT 
	seikyuCsv.group, 
	seikyuCsv.groupNo,  
	? as hakko_bi, 
	seikyuCsv.seikyu_cd, 
	seikyuCsv.nyukin_cd, 
	seikyuCsv.uriage_cd, 
	bpgroup.name, 
	bpgroup.sekyusaki_cd, 
	shimekbn.name, 
	seikyuCsv.nengetu, 
	to_char(seikyuCsv.kaisyu_yoteibi, 'YYYY/MM/DD') as kaisyu_yoteibi, 
	seikyuCsv.zenkaiseikyu, 
	seikyuCsv.zenkainyukin, 
	seikyuCsv.kurikosi, 
	seikyuCsv.zeinuki, 
	seikyuCsv.syohizei, 
	seikyuCsv.seikyukingak, 
	seikyuCsv.ruikeiseikyu, 
	to_char(seikyuCsv.nyukin_bi, 'YYYY/MM/DD') as nyukin_bi, 
	nyukinkbn.name, 
	kozakbn.biko, 
	seikyuCsv.nyukingak, 
	seikyuCsv.sosai, 
	seikyuCsv.syouhizei_chosei, 
	seikyuCsv.hurikomi_tesuryo, 
	seikyuCsv.hanbai_tesuryo, 
	seikyuCsv.sozei_koka, 
	seikyuCsv.shomohin, 
	seikyuCsv.gaitonashi, 
	seikyuCsv.nyukingokei, 
	seikyuCsv.nyukinmemo, 
	to_char(seikyuCsv.uriage_bi, 'YYYY/MM/DD') as uriage_bi, 
	bplocation.nameryaku, 
	bplocation.syukasaki_cd, 
	uriagekbn.name, 
	seikyuCsv.uriagegokeikingak, 
	seikyuCsv.uriagesyouhizei 
FROM ( 
	( select  
		'請求' as group,  
		1 as groupNo,  
		seikyu_cd,  
		null as nyukin_cd,  
		null as uriage_cd, 
		nengetu as nengetu, 
		zenkai_zandaka + zenkai_nyukingak as zenkaiseikyu, 
		zenkai_nyukingak as zenkainyukin, 
		zenkai_zandaka as kurikosi, 
		seikyu_kingak as zeinuki, 
		syohizei as syohizei, 
		seikyu_kingak + syohizei as seikyukingak, 
		ruikei_seikyu as ruikeiseikyu, 
		kaisyu_yoteibi as kaisyu_yoteibi, 
		c_bp_group_id as c_bp_group_id, 
		null as nyukin_bi, 
		null as nyukin_kbn, 
		null as nyukin_koza, 
		null as nyukingak, 
		null as sosai, 
		null as syouhizei_chosei, 
		null as hurikomi_tesuryo, 
		null as hanbai_tesuryo, 
		null as sozei_koka, 
		null as shomohin, 
		null as gaitonashi, 
		null as nyukingokei, 
		null as nyukinmemo, 
		null as uriage_bi, 
		null as c_bpartner_location_id, 
		null as uriage_kbn, 
		null as uriagegokeikingak, 
		null as uriagesyouhizei 
from euc_seikyu ) 
union 
	( select  
		'入金' as group,  
		2 as groupNo,  
		seikyu_cd,  
		nyukin_cd,  
		null, null, null, null, null, null, null, null, null, null, null, 
		nyukin_bi as nyukin_bi, 
		nyukin_kbn as nyukin_kbn, 
		nyukin_koza as nyukin_koza, 
		nyukingak as nyukingak, 
		sosai as sosai, 
		syouhizei_chosei as syouhizei_chosei, 
		hurikomi_tesuryo as hurikomi_tesuryo, 
		hanbai_tesuryo as hanbai_tesuryo, 
		sozei_koka as sozei_koka, 
		shomohin as shomohin, 
		gaitonashi as gaitonashi, 
		(nyukingak + sosai + syouhizei_chosei + hurikomi_tesuryo + hanbai_tesuryo + sozei_koka + shomohin + gaitonashi) as nyukingokei, 
		memo as nyukinmemo, 
		null::timestamp, 
		null::numeric, 
		null, 
		null::numeric, 
		null::numeric 
	from euc_nyukin where seikyu_cd <> '' ) 
union 
	( select  
		'売上' as group,  
		3 as groupNo, 
		seikyu_cd, 
		null, 
		uriage_cd, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		uriage_bi as uriage_bi, 
		c_bpartner_location_id as c_bpartner_location_id, 
		uriage_kbn as uriage_kbn, 
		uriagegokeikingak as uriagegokeikingak, 
		syouhizei as uriagesyouhizei from euc_uriage where seikyu_cd <> '' ) ) as seikyuCsv 
left join c_bp_group bpgroup 
	on seikyuCsv.c_bp_group_id = bpgroup.c_bp_group_id 
left join c_bpartner_location bplocation 
	on seikyuCsv.c_bpartner_location_id = bplocation.c_bpartner_location_id 
left join euc_kbn uriagekbn 
	on uriagekbn.kbn_cd = '7' and seikyuCsv.uriage_kbn = uriagekbn.cd 
left join euc_kbn kozakbn 
	on kozakbn.kbn_cd = '70' and seikyuCsv.nyukin_koza = kozakbn.cd 
left join euc_kbn shimekbn 
	on shimekbn.kbn_cd = '5' and bpgroup.sime_kbn = shimekbn.cd 
left join euc_kbn nyukinkbn 
	on nyukinkbn.kbn_cd = '46' and seikyuCsv.nyukin_kbn = nyukinkbn.cd 
AND A.sakujo_kbn=? AND A.nengetu=? AND B.sime_kbn=? 

















SELECT 
	seikyuCsv.group, 
	seikyuCsv.groupNo, 
	? as hakko_bi, 
	seikyuCsv.seikyu_cd, 
	seikyuCsv.nyukin_cd, 
	seikyuCsv.uriage_cd, 
	bpgroup.name, 
	bpgroup.sekyusaki_cd, 
	shimekbn.name, 
	seikyuCsv.nengetu, 
	to_char(seikyuCsv.kaisyu_yoteibi, 'YYYY/MM/DD') as kaisyu_yoteibi, 
	seikyuCsv.zenkaiseikyu, 
	seikyuCsv.zenkainyukin, 
	seikyuCsv.kurikosi, 
	seikyuCsv.zeinuki, 
	seikyuCsv.syohizei, 
	seikyuCsv.seikyukingak, 
	seikyuCsv.ruikeiseikyu, 
	to_char(seikyuCsv.nyukin_bi, 'YYYY/MM/DD') as nyukin_bi, 
	nyukinkbn.name, 
	kozakbn.biko, 
	seikyuCsv.nyukingak, 
	seikyuCsv.sosai, 
	seikyuCsv.syouhizei_chosei, 
	seikyuCsv.hurikomi_tesuryo, 
	seikyuCsv.hanbai_tesuryo, 
	seikyuCsv.sozei_koka, 
	seikyuCsv.shomohin, 
	seikyuCsv.gaitonashi, 
	seikyuCsv.nyukingokei, 
	seikyuCsv.nyukinmemo, 
	to_char(seikyuCsv.uriage_bi, 'YYYY/MM/DD') as uriage_bi, 
	bplocation.nameryaku, 
	bplocation.syukasaki_cd, 
	uriagekbn.name, 
	seikyuCsv.uriagegokeikingak, 
	seikyuCsv.uriagesyouhizei 
FROM ( 
	( select 
		'請求' as group, 
		1 as groupNo, 
		seikyu_cd, 
		null as nyukin_cd, 
		null as uriage_cd, 
		nengetu as nengetu, 
		zenkai_zandaka + zenkai_nyukingak as zenkaiseikyu, 
		zenkai_nyukingak as zenkainyukin, 
		zenkai_zandaka as kurikosi, 
		seikyu_kingak as zeinuki, 
		syohizei as syohizei, 
		seikyu_kingak + syohizei as seikyukingak, 
		ruikei_seikyu as ruikeiseikyu, 
		kaisyu_yoteibi as kaisyu_yoteibi, 
		c_bp_group_id as c_bp_group_id, 
		null as nyukin_bi, 
		null as nyukin_kbn, 
		null as nyukin_koza, 
		null as nyukingak, 
		null as sosai, 
		null as syouhizei_chosei, 
		null as hurikomi_tesuryo, 
		null as hanbai_tesuryo, 
		null as sozei_koka, 
		null as shomohin, 
		null as gaitonashi, 
		null as nyukingokei, 
		null as nyukinmemo, 
		null as uriage_bi, 
		null as c_bpartner_location_id, 
		null as uriage_kbn, 
		null as uriagegokeikingak, 
		null as uriagesyouhizei 
	from euc_seikyu ) 
union 
	( select 
		'入金' as group, 
		2 as groupNo, 
		seikyu_cd, 
		nyukin_cd, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		nyukin_bi as nyukin_bi, 
		nyukin_kbn as nyukin_kbn, 
		nyukin_koza as nyukin_koza, 
		nyukingak as nyukingak, 
		sosai as sosai, 
		syouhizei_chosei as syouhizei_chosei, 
		hurikomi_tesuryo as hurikomi_tesuryo, 
		hanbai_tesuryo as hanbai_tesuryo, 
		sozei_koka as sozei_koka, 
		shomohin as shomohin, 
		gaitonashi as gaitonashi, 
		(nyukingak + sosai + syouhizei_chosei + hurikomi_tesuryo + hanbai_tesuryo + sozei_koka + shomohin + gaitonashi) as nyukingokei, 
		memo as nyukinmemo, 
		null::timestamp, 
		null::numeric, 
		null, 
		null::numeric, 
		null::numeric 
	from euc_nyukin where seikyu_cd <> '' ) 
union 
	( select 
		'売上' as group, 
		3 as groupNo, 
		seikyu_cd, 
		null, 
		uriage_cd, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		null, 
		uriage_bi as uriage_bi, 
		c_bpartner_location_id as c_bpartner_location_id, 
		uriage_kbn as uriage_kbn, 
		uriagegokeikingak as uriagegokeikingak, 
		syouhizei as uriagesyouhizei 
	from euc_uriage where seikyu_cd <> '' ) ) as seikyuCsv 

left join c_bp_group bpgroup 
	on seikyuCsv.c_bp_group_id = bpgroup.c_bp_group_id 
left join c_bpartner_location bplocation 
	on seikyuCsv.c_bpartner_location_id = bplocation.c_bpartner_location_id 
left join euc_kbn uriagekbn 
	on uriagekbn.kbn_cd = '7' and seikyuCsv.uriage_kbn = uriagekbn.cd 
left join euc_kbn kozakbn 
	on kozakbn.kbn_cd = '70' and seikyuCsv.nyukin_koza = kozakbn.cd 
left join euc_kbn shimekbn 
	on shimekbn.kbn_cd = '5' and bpgroup.sime_kbn = shimekbn.cd 
left join euc_kbn nyukinkbn 
	on nyukinkbn.kbn_cd = '46' and seikyuCsv.nyukin_kbn = nyukinkbn.cd 

































