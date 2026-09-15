import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { ShuffleLunch } from '@/script/Pages/ShuffleLunch/ShuffleLunch';
import {
  E_TIME_ZONE,
  ETimeZone,
} from '@/script/Enum/Server/App/ShuffleLunch/ETimeZone';
import {
  AppTrnShuffleLunchEntryList,
  IAppTrnShuffleLunchEntry,
  parseAppTrnShuffleLunchEntryPayload,
} from '@/script/Models/App/Trn/TrnShuffleLunchEntry';
import {
  E_CALCULATE,
  ECalculate,
} from '@/script/Enum/Server/App/ShuffleLunch/ECalculate';
import {
  AppTrnShuffleLunchGroupList,
  IAppTrnShuffleLunchGroup,
  parseAppTrnShuffleLunchGroupPayload,
} from '@/script/Models/App/Trn/TrnShuffleLunchGroup';

/**
 * コントローラーからグローバルに受取保持する値.
 */
type IndexContext = {
  timezone: ETimeZone;
  calculate: ECalculate;
  trnShuffleLunchEntryList: AppTrnShuffleLunchEntryList;
  trnShuffleLunchGroupList: AppTrnShuffleLunchGroupList;
  message: string;
};
export const context = React.createContext<IndexContext>({
  timezone: E_TIME_ZONE.INVALID,
  calculate: E_CALCULATE.INVALID,
  trnShuffleLunchEntryList: new AppTrnShuffleLunchEntryList(),
  trnShuffleLunchGroupList: new AppTrnShuffleLunchGroupList(),
  message: '',
});
export const useIndexContext = (): IndexContext => useContext(context);

interface IProps extends IPropsBase {
  timezone: number;
  calculate: number;
  trnShuffleLunchEntryList: IAppTrnShuffleLunchEntry[];
  trnShuffleLunchGroupList: IAppTrnShuffleLunchGroup[];
  message: string;
}
export const Index: React.FC<IProps> = ({
  timezone,
  calculate,
  trnShuffleLunchEntryList,
  trnShuffleLunchGroupList,
  message,
  ...props
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      // マッチング情報.
      timezone: timezone as ETimeZone,
      calculate: calculate as ECalculate,

      // 本日のエントリー.
      trnShuffleLunchEntryList: new AppTrnShuffleLunchEntryList(
        trnShuffleLunchEntryList.map(v =>
          parseAppTrnShuffleLunchEntryPayload(v),
        ),
      ),

      // 本日のグループ.
      trnShuffleLunchGroupList: new AppTrnShuffleLunchGroupList(
        trnShuffleLunchGroupList.map(v =>
          parseAppTrnShuffleLunchGroupPayload(v),
        ),
      ),

      // エラーメッセージ.
      message,
    }),
    [timezone, trnShuffleLunchEntryList],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <context.Provider value={providerValue}>
        <ShuffleLunch />
      </context.Provider>
    </CommonIndexProvider>
  );
};
export default Index;
