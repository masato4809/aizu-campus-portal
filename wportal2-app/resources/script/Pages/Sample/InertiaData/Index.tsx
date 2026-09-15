import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { InertiaData } from '@/script/Pages/Sample/InertiaData/InertiaData';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';

/**
 * コントローラーから受取保持する値.
 */
type IndexContext = {
  providerArray: IAppTrnUser[];
  trnUserList: AppTrnUserList;
};

export const context = React.createContext<IndexContext>({
  providerArray: [],
  trnUserList: new AppTrnUserList(),
});
export const useIndexContext = (): IndexContext => useContext(context);

/**
 * コントローラーから受け取るprops.
 */
interface IPros extends IPropsBase {
  fixArray: object[];
  sqlArray: object[];
  eloquentArray: object[];
  payloadArray: IAppTrnUser[];
  providerArray: IAppTrnUser[];
  parseArray: IAppTrnUser[];
}
export const Index: React.FC<IPros> = ({
  fixArray,
  sqlArray,
  eloquentArray,
  payloadArray,
  providerArray,
  parseArray,
  ...props
}) => {
  /**
   * ①データ、fixArrayのPropsでデータを受け取り、直接参照が可能
   * このデータは値を次のコンポーネントにpropsで渡す.
   */
  console.log('[fixArray]での受信.');
  console.table(fixArray);

  /**
   * ②データ、sqlArrayのPropsでデータを受け取り、直接参照が可能.
   * このデータは値を次のコンポーネントにpropsで渡す.
   */
  console.log('[sqlArray]での受信.');
  console.table(sqlArray);

  /**
   * ③データ、eloquentArrayのPropsでデータを受け取り、直接参照が可能.
   * このデータは値を次のコンポーネントにpropsで渡す.
   */
  console.log('[eloquentArray]での受信.');
  console.table(eloquentArray);

  /**
   * ④データ、payloadArrayのPropsでデータを受け取り、直接参照が可能.
   * 受け取る方をIAppTrnUserとして認識させている.
   */
  console.log('[payloadArray]での受信.');
  console.table(payloadArray);

  /**
   * ⑤データ、providerArrayのPropsでデータを受け取り、直接参照が可能.
   * この後Providerで保持して、propsでの伝達は行わない.
   */
  console.log('[providerArray]での受信.');
  console.table(providerArray);

  /**
   * ⑥データ、parseArrayのPropsでデータを受け取り、直接参照が可能.
   * この後、サーバーの値を信用せずに変換をかけてからProviderで保持する.
   */
  console.log('[parseArray]での受信.');
  console.table(parseArray);

  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: IndexContext = useMemo(
    () => ({
      providerArray,
      /**
       * サーバーの値を信用せずに、各値をチェックし
       * 信用できる値に変換してからProviderで保持する.
       */
      trnUserList: new AppTrnUserList(
        parseArray.map(v => parseAppTrnUserPayload(v)),
      ),
    }),
    [providerArray],
  );

  // noinspection TypeScriptValidateTypes
  return (
    <context.Provider value={providerValue}>
      {}
      <CommonIndexProvider {...props}>
        <InertiaData
          fixArray={fixArray}
          sqlArray={sqlArray}
          eloquentArray={eloquentArray}
          payloadArray={payloadArray}
        />
      </CommonIndexProvider>
    </context.Provider>
  );
};
export default Index;
