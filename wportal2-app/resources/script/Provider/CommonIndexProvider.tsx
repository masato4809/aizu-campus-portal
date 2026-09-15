import * as React from 'react';
import { CssBaseline, ThemeProvider } from '@mui/material';
import { ApolloClient, ApolloProvider, InMemoryCache } from '@apollo/client';
import { useContext, useMemo } from 'react';
import createUploadLink from 'apollo-upload-client/createUploadLink.mjs';
import { IPropsBase } from '@/script/System/System';
import { themeCommon } from '@/script/System/Theme';
import { ProgressProvider } from '@/script/Provider/ProgressProvider';
import {
  getDefaultLocation,
  ILocation,
  parseLocation,
} from '@/script/Provider/ILocation';
import {
  AppAuthUserList,
  IAppAuthUser,
  parseAppAuthUserPayload,
} from '@/script/Models/App/Auth/AuthUserList';
import {
  AppTrnUserAuthorityList,
  IAppTrnUserAuthority,
  parseAppTrnUserAuthorityPayload,
} from '@/script/Models/App/Trn/TrnUserAuthorityList';
import { SnackbarProvider } from '@/script/Provider/SnackbarProvider';

const link = createUploadLink({
  uri: '/graphql',
});

const client = new ApolloClient({
  ssrMode: false,
  cache: new InMemoryCache(),
  link,
});

/**
 * コントローラーからグローバルに受取保持する値.
 */
type CommonIndexContext = {
  csrfToken: string;
  authUser: IAppAuthUser;
  location: ILocation;
  trnUserAuthorityList: AppTrnUserAuthorityList;
  achievementCount: number;
};
export const context = React.createContext<CommonIndexContext>({
  csrfToken: '',
  authUser: AppAuthUserList.defaultInterface(),
  location: getDefaultLocation(),
  trnUserAuthorityList: new AppTrnUserAuthorityList([]),
  achievementCount: 0,
});
export const useCommonIndexContext = (): CommonIndexContext =>
  useContext(context);

interface IProps extends IPropsBase {
  csrfToken?: string;
  authUser?: IAppAuthUser;
  location?: ILocation;
  trnUserAuthorityList?: IAppTrnUserAuthority[];
  achievementCount?: number;
}
export const CommonIndexProvider: React.FC<IProps> = ({
  csrfToken,
  authUser,
  location,
  trnUserAuthorityList,
  achievementCount,
  children,
}) => {
  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: CommonIndexContext = useMemo(
    () => ({
      csrfToken: csrfToken ?? '',
      authUser: parseAppAuthUserPayload(authUser),
      location: parseLocation(location),
      trnUserAuthorityList: new AppTrnUserAuthorityList(
        trnUserAuthorityList?.map(v => parseAppTrnUserAuthorityPayload(v)),
      ),
      achievementCount: achievementCount ?? 0,
    }),
    [csrfToken, authUser, location, trnUserAuthorityList, achievementCount],
  );

  return (
    <context.Provider value={providerValue}>
      <ThemeProvider theme={themeCommon}>
        <ApolloProvider client={client}>
          <ProgressProvider>
            <SnackbarProvider>
              <CssBaseline />
              {children}
            </SnackbarProvider>
          </ProgressProvider>
        </ApolloProvider>
      </ThemeProvider>
    </context.Provider>
  );
};
